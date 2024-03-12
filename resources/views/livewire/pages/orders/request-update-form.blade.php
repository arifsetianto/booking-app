<?php

use App\Event\Order\OrderUpdated;
use App\Livewire\Forms\Order\ForceUpdateOrderForm;
use App\Models\City;
use App\Models\Designation;
use App\Models\District;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Region;
use App\Models\Religion;
use App\Models\Shipping;
use App\Models\Source;
use App\Models\SubDistrict;
use App\ValueObject\Gender;
use App\ValueObject\OrderStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use function Livewire\Volt\{state};

new class extends Component {
    use WithFileUploads;

    public ?Order $order;
    public array $sources = [];
    public array $designations = [];
    public array $genders = [];
    public array $religions = [];
    public array $regions = [];
    public array $cities = [];
    public array $districts = [];
    public array $subDistricts = [];
    public string $receiverIdentityFile;
    public string $receiptFile;

    public ForceUpdateOrderForm $form;

    public function mount(Request $request): void
    {
        $this->order = Order::where('id', $request->route('order'))->first();

        $this->sources = Source::get()->map(fn($item) => ['value' => $item->id, 'label' => $item->name])->toArray();
        $this->designations =
            Designation::get()->map(fn($item) => ['value' => $item->id, 'label' => $item->name])->toArray();
        $this->genders = Gender::getOptions();
        $this->religions = Religion::get()->map(fn($item) => ['value' => $item->id, 'label' => $item->name])->toArray();
        $this->regions = Region::get()->map(fn($item) => ['value' => $item->id, 'label' => $item->name])->toArray();
        $this->receiverIdentityFile = Storage::url($this->order->orderItem->identity_file);
        $this->receiptFile = Storage::url($this->order->payment->receipt_file);

        $this->form->email = $this->order->email;
        $this->form->name = $this->order->name;
        $this->form->phone = $this->order->phone;
        $this->form->instagram = $this->order->instagram;
        $this->form->source = $this->order->source->id;
        $this->form->comment = $this->order->comment;
        $this->form->receiverEnName = $this->order->orderItem->receiver_en_name;
        $this->form->receiverThName = $this->order->orderItem->receiver_th_name;
        $this->form->designation = $this->order->orderItem->designation->id;
        $this->form->gender = $this->order->orderItem->gender->value;
        $this->form->religion = $this->order->orderItem->religion->id;
        $this->form->receiverName = $this->order->shipping->name;
        $this->form->receiverPhone = $this->order->shipping->phone;
        $this->form->address = $this->order->shipping->address;
        $this->form->region = $this->order->shipping->subDistrict?->district?->city?->region?->id;
        $this->form->city = $this->order->shipping->subDistrict?->district?->city?->id;
        $this->form->district = $this->order->shipping->subDistrict?->district?->id;
        $this->form->subDistrict = $this->order->shipping->subDistrict?->id;
        $this->form->fee = $this->order->shipping->fee;

        if ($this->order->shipping->subDistrict?->district?->city) {
            $this->getCitiesByRegion();
        }

        if ($this->order->shipping->subDistrict?->district) {
            $this->getDistrictsByCity();
        }

        if ($this->order->shipping->subDistrict) {
            $this->getSubDistrictsByDistrict();
        }
    }

    public function getCitiesByRegion(): void
    {
        $this->cities = City::where('region_id', $this->form->region)->get()->map(
            fn($item) => ['value' => $item->id, 'label' => sprintf('%s (%s)', $item->th_name, $item->en_name)]
        )->toArray();
    }

    public function getDistrictsByCity(): void
    {
        $this->districts =
            District::where('city_id', $this->form->city)->get()->map(
                fn($item) => ['value' => $item->id, 'label' => sprintf('%s (%s)', $item->th_name, $item->en_name)]
            )->toArray();
    }

    public function getSubDistrictsByDistrict(): void
    {
        $this->subDistricts = SubDistrict::where('district_id', $this->form->district)->get()->map(
            fn($item) => ['value' => $item->id, 'label' => sprintf('%s (%s)', $item->th_name, $item->en_name)]
        )->toArray();
    }

    public function update(): void
    {
        $this->form->validate();

        if (!$this->order->status->is(OrderStatus::REVISED)) {
            $this->redirectRoute(name: 'orders.update.error', parameters: ['order' => $this->order->id]);
        } else {
            /** @var Order $order */
            $order = Order::findOrFail($this->order->id);
            $order->email = $this->form->email;
            $order->name = $this->form->name;
            $order->phone = $this->form->phone;
            $order->instagram = $this->form->instagram;
            $order->source()->associate(Source::find($this->form->source));
            $order->comment = $this->form->comment;
            $order->status = OrderStatus::CONFIRMED;
            $order->confirmed_at = Carbon::now();
            $order->reason = null;
            $order->revised_at = null;

            $order->save();

            /** @var OrderItem $item */
            $item = OrderItem::findOrFail($this->order->orderItem->id);
            $item->receiver_en_name = $this->form->receiverEnName;
            $item->receiver_th_name = $this->form->receiverThName;
            $item->gender = Gender::from($this->form->gender);
            $item->religion()->associate(Religion::find($this->form->religion));
            $item->designation()->associate(Designation::find($this->form->designation));
            $item->order()->associate($order);

            if ($this->form->identityFile) {
                if (Storage::exists($item->identity_file)) {
                    Storage::delete($item->identity_file);
                }

                $item->identity_file = $this->form->identityFile->store('orders/identities');
            }

            $item->save();

            /** @var Shipping $shipping */
            $shipping = Shipping::findOrFail($this->order->shipping->id);
            $shipping->name = $this->form->receiverName;
            $shipping->phone = $this->form->receiverPhone;
            $shipping->address = $this->form->address;
            $shipping->subDistrict()->associate(SubDistrict::findOrFail($this->form->subDistrict));
            $shipping->fee = $this->form->fee;

            $shipping->save();

            /** @var Payment $payment */
            $payment = Payment::findOrFail($this->order->payment->id);

            if ($this->form->receiptFile) {
                if (Storage::exists($payment->receipt_file)) {
                    Storage::delete($payment->receipt_file);
                }

                $payment->receipt_file = $this->form->receiptFile->store('payments/receipts');
                $payment->paid_at = Carbon::now();
            }

            $payment->save();

            event(new OrderUpdated($order));

            if (Storage::directoryExists('livewire-tmp')) {
                Storage::deleteDirectory('livewire-tmp');
            }

            $this->redirectRoute(name: 'orders.update.success', parameters: ['order' => $order->id]);
        }
    }
};

?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Booking Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("To proceed with this booking process, please correctly complete the following details so that we can verify your order.") }}
        </p>
    </header>

    @if($order->reason)
        <div class="p-4 my-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300"
             role="alert">
            <span class="font-medium">{{ $order->reason }}</span>
        </div>
    @endif

    <form wire:submit="update" class="mt-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="email" :value="__('Email')"/>
                <x-text-input wire:model="form.email" id="email" name="email" type="email" class="mt-1 block w-full"
                              autofocus autocomplete="email" placeholder="Please enter your valid email address"/>
                <x-input-error class="mt-2" :messages="$errors->get('form.email')"/>
            </div>
            <div>
                <x-input-label for="name" :value="__('Full Name')"/>
                <x-text-input wire:model="form.name" id="name" name="name" type="text" class="mt-1 block w-full"
                              autofocus autocomplete="name" placeholder="Please enter your full name"/>
                <x-input-error class="mt-2" :messages="$errors->get('form.name')"/>
            </div>
            <div>
                <x-input-label for="phone" :value="__('Mobile No.')"/>
                <x-text-input wire:model="form.phone" id="phone" name="phone" type="text" class="mt-1 block w-full"
                              autofocus autocomplete="phone" placeholder="Please enter your mobile number"/>
                <x-input-error class="mt-2" :messages="$errors->get('form.phone')"/>
            </div>
            <div>
                <x-input-label for="instagram" :value="__('Instagram Account')"/>
                <x-text-input wire:model="form.instagram" id="instagram" name="instagram" type="text"
                              class="mt-1 block w-full"
                              autofocus autocomplete="instagram" placeholder="Please enter your instagram account"/>
                <x-input-error class="mt-2" :messages="$errors->get('form.instagram')"/>
            </div>
            <div>
                <x-input-label for="source" :value="__('How do you know us')"/>
                <x-select-input wire:model="form.source" id="source" name="source" class="mt-1 block w-full"
                                :options="$sources"
                                autofocus/>
                <x-input-error class="mt-2" :messages="$errors->get('form.source')"/>
            </div>
            <div>
                <x-input-label for="comment" :value="__('Comment')"/>
                <x-text-area wire:model="form.comment" id="comment" name="comment" class="mt-1 block w-full"
                             autofocus autocomplete="comment"/>
                <x-input-error class="mt-2" :messages="$errors->get('form.comment')"/>
            </div>
        </div>

        <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="receiver_en_name" :value="__('Receiver Name in English')"/>
                <x-text-input wire:model="form.receiverEnName" id="receiver_en_name" name="receiver_en_name" type="text"
                              class="mt-1 block w-full"
                              autofocus autocomplete="receiver_en_name"
                              placeholder="Please enter receiver name in english"/>
                <x-input-error class="mt-2" :messages="$errors->get('form.receiverEnName')"/>
            </div>
            <div>
                <x-input-label for="receiver_th_name" :value="__('Receiver Name in Thai')"/>
                <x-text-input wire:model="form.receiverThName" id="receiver_th_name" name="receiver_th_name" type="text"
                              class="mt-1 block w-full"
                              autofocus autocomplete="receiver_th_name"
                              placeholder="Please enter receiver name in thai"/>
                <x-input-error class="mt-2" :messages="$errors->get('form.receiverThName')"/>
            </div>
            <div>
                <x-input-label for="designation" :value="__('Order For')"/>
                <x-select-input wire:model="form.designation" id="designation" name="designation"
                                class="mt-1 block w-full"
                                :options="$designations"
                                autofocus/>
                <x-input-error class="mt-2" :messages="$errors->get('form.designation')"/>
            </div>
            <div>
                <x-input-label for="gender" :value="__('Gender')"/>
                <x-select-input wire:model="form.gender" id="gender" name="gender"
                                class="mt-1 block w-full"
                                :options="$genders"
                                autofocus/>
                <x-input-error class="mt-2" :messages="$errors->get('form.gender')"/>
            </div>
            <div>
                <x-input-label for="religion" :value="__('Religion')"/>
                <x-select-input wire:model="form.religion" id="religion" name="religion" class="mt-1 block w-full"
                                :options="$religions"
                                autofocus/>
                <x-input-error class="mt-2" :messages="$errors->get('form.religion')"/>
            </div>
            <div>
                <x-input-label for="identity_file" :value="__('Receiver Thai ID')"/>
                <input wire:model="form.identityFile"
                       class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                       aria-describedby="identity_file_help" id="identity_file" type="file"
                       accept="image/png, image/jpg, image/jpeg">
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="identity_file_help">jpeg, jpg, or png (max.
                    2MB).</p>
                <x-input-error class="mt-2" :messages="$errors->get('form.identityFile')"/>
            </div>
            <div>
                <figure class="max-w-lg">
                    <img class="h-auto max-w-sm rounded-lg"
                         src="{{ $receiverIdentityFile }}" alt="">
                    <figcaption class="mt-2 text-sm text-center text-gray-500 dark:text-gray-400">Receiver Thai ID
                    </figcaption>
                </figure>
            </div>
        </div>

        <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">

        <header>
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Delivery Information') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __("Please fill in the shipping information correctly so that we can send the order immediately.") }}
            </p>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="receiverName" :value="__('Receiver Name same as ID')"/>
                <x-text-input wire:model="form.receiverName" id="receiverName" name="receiverName" type="text"
                              class="mt-1 block w-full"
                              autofocus autocomplete="receiverName"
                              placeholder="Please enter receiver name same as ID"/>
                <x-input-error class="mt-2" :messages="$errors->get('form.receiverName')"/>
            </div>
            <div>
                <x-input-label for="receiverPhone" :value="__('Receiver Phone')"/>
                <x-text-input wire:model="form.receiverPhone" id="receiverPhone" name="receiverPhone" type="text"
                              class="mt-1 block w-full"
                              autofocus autocomplete="receiverPhone"
                              placeholder="Please enter receiver phone"/>
                <x-input-error class="mt-2" :messages="$errors->get('form.receiverPhone')"/>
            </div>
            <div>
                <x-input-label for="address" :value="__('Address')"/>
                <x-text-area wire:model="form.address" id="address" name="address" class="mt-1 block w-full"
                             autofocus autocomplete="address"/>
                <x-input-error class="mt-2" :messages="$errors->get('form.address')"/>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="region" :value="__('Region')"/>
                <x-select-input wire:model.live="form.region" wire:change="getCitiesByRegion" id="region" name="region"
                                class="mt-1 block w-full"
                                :options="$regions"
                                autofocus/>
                <x-input-error class="mt-2" :messages="$errors->get('form.region')"/>
            </div>
            <div>
                <x-input-label for="city" :value="__('City')"/>
                <x-select-input wire:model.live="form.city" wire:key="{{ $form->region }}"
                                wire:change="getDistrictsByCity"
                                id="city" name="city" class="mt-1 block w-full"
                                :options="$cities"
                                autofocus/>
                <x-input-error class="mt-2" :messages="$errors->get('form.city')"/>
            </div>
            <div>
                <x-input-label for="district" :value="__('District')"/>
                <x-select-input wire:model.live="form.district" wire:key="{{ $form->city }}"
                                wire:change="getSubDistrictsByDistrict" id="district" name="district"
                                class="mt-1 block w-full"
                                :options="$districts"
                                autofocus/>
                <x-input-error class="mt-2" :messages="$errors->get('form.district')"/>
            </div>
            <div>
                <x-input-label for="subDistrict" :value="__('Sub District')"/>
                <x-select-input wire:model="form.subDistrict" wire:key="{{ $form->district }}"
                                id="subDistrict" name="subDistrict"
                                class="mt-1 block w-full"
                                :options="$subDistricts"
                                autofocus/>
                <x-input-error class="mt-2" :messages="$errors->get('form.subDistrict')"/>
            </div>

            <div>
                <x-input-label for="delivery_fee" :value="__('Delivery Fee')"/>
                <div class="flex mt-1 w-full">
                  <span
                      class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border rounded-e-0 border-gray-300 rounded-s-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                    THB
                  </span>
                    <x-text-input wire:model="form.fee" id="delivery_fee" name="delivery_fee" type="text"
                                  class="block w-full rounded-none rounded-e-lg bg-gray-50 border text-gray-900 flex-1 min-w-0 text-sm border-gray-300 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                  autofocus autocomplete="delivery_fee" readonly/>
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('form.fee')"/>
            </div>
        </div>

        <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">

        <header>
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Payment Information') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __("Please upload proof of transfer file correctly so that we can verify the payment order immediately.") }}
            </p>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
            <div>
                <figure class="max-w-lg">
                    <img class="h-auto max-w-sm mx-auto rounded-lg"
                         src="{{ asset('images/qr-payment.png') }}" alt="">
                    <figcaption class="mt-2 text-sm text-center text-gray-500 dark:text-gray-400">QR Bank ThaiQuran
                    </figcaption>
                </figure>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">Bank Name</p>
                <p class="pt-1 text-sm">Krungthai Bank</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Account Name</p>
                <p class="pt-1 text-sm">ThaiQuran Foundation</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Account Number</p>
                <p class="pt-1 text-sm">819-0-47810-9</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Delivery Fee</p>
                <p class="pt-1 text-sm">THB 100</p>
            </div>
            <div>
                <x-input-label for="receipt_file" :value="__('Upload Payment Receipt')"/>
                <input wire:model="form.receiptFile"
                       class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                       aria-describedby="receipt_file_help" id="receipt_file" type="file"
                       accept="image/png, image/jpg, image/jpeg">
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="receipt_file_help">jpeg, jpg,
                    or png
                    (max.
                    2MB).</p>
                <x-input-error class="mt-2" :messages="$errors->get('form.receiptFile')"/>
            </div>
            <div>
                <figure class="max-w-lg">
                    <img class="h-auto max-w-sm rounded-lg"
                         src="{{ $receiptFile }}" alt="">
                    <figcaption class="mt-2 text-sm text-center text-gray-500 dark:text-gray-400">Payment Receipt File
                    </figcaption>
                </figure>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Submit Now') }}</x-primary-button>
        </div>
    </form>
</section>
