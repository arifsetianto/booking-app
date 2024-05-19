<?php

use App\Event\Order\OrderInvitationConfirmed;
use App\Livewire\Forms\Order\CreateOrderFromInvitationForm;
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
use App\Models\User;
use App\ValueObject\Gender;
use App\ValueObject\OrderStatus;
use App\ValueObject\PaymentStatus;
use App\ValueObject\UserStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use function Livewire\Volt\{state};

new class extends Component {
    use WithFileUploads;

    public Order $order;
    public array $sources = [];
    public array $designations = [];
    public array $genders = [];
    public array $religions = [];
    public array $regions = [];
    public array $cities = [];
    public array $districts = [];
    public array $subDistricts = [];

    public CreateOrderFromInvitationForm $form;

    public function mount(Request $request): void
    {
        $this->order = Order::findOrFail($request->route('order'));

        $this->form->name = $this->order->name;
        $this->form->phone = $this->order->phone;
        $this->form->instagram = $this->order->instagram;
        $this->form->source = $this->order->source?->id;
        $this->form->comment = $this->order->comment;
        $this->form->receiverEnName = $this->order->orderItem?->receiver_en_name;
        $this->form->receiverThName = $this->order->orderItem?->receiver_th_name;
        $this->form->designation = $this->order->orderItem?->designation?->id;
        $this->form->religion = $this->order->orderItem?->religion?->id;
        $this->form->gender = $this->order->orderItem?->gender?->value;
        //$this->form->identityFile = $this->order->orderItem?->identity_file ? TemporaryUploadedFile::createFromLivewire($this->order->orderItem?->identity_file) : null;
        $this->form->receiverPhone = $this->order->shipping?->phone;
        $this->form->address = $this->order->shipping?->address;
        $this->form->region = $this->order->shipping?->subDistrict?->district?->city?->region?->id;
        $this->form->city = $this->order->shipping?->subDistrict?->district?->city?->id;
        $this->form->district = $this->order->shipping?->subDistrict?->district?->id;
        $this->form->subDistrict = $this->order->shipping?->subDistrict?->id;
        $this->form->zipCode = $this->order->shipping?->subDistrict?->zip_code;
        $this->form->fee = 0;

        $this->sources = Source::get()->map(fn($item) => ['value' => $item->id, 'label' => $item->name])->toArray();
        $this->designations =
            Designation::orderBy('number')
                       ->get()
                       ->map(fn($item) => ['value' => $item->id, 'label' => $item->name])
                       ->toArray();
        $this->genders = Gender::getOptions();
        $this->religions = Religion::get()->map(fn($item) => ['value' => $item->id, 'label' => $item->name])->toArray();
        $this->regions =
            Region::get()->map(
                fn($item) => ['value' => $item->id, 'label' => sprintf('%s (%s)', $item->th_name, $item->en_name)]
            )->toArray();

        if ($this->order->shipping?->subDistrict?->district?->city) {
            $this->getCitiesByRegion();
        }

        if ($this->order->shipping?->subDistrict?->district) {
            $this->getDistrictsByCity();
        }

        if ($this->order->shipping?->subDistrict) {
            $this->getSubDistrictsByDistrict();
            $this->selectSubDistrict();
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

    public function selectSubDistrict(): void
    {
        if ('' !== $this->form->subDistrict) {
            $subDistrict = SubDistrict::findOrFail($this->form->subDistrict);

            //$this->form->fee = 100;
            $this->form->zipCode = $subDistrict->zip_code;
        } else {
            //$this->form->fee = 0;
            $this->form->zipCode = '';
        }
    }

    public function save(): void
    {
        $this->form->validate();

        /** @var Order $order */
        $order = Order::findOrFail($this->order->id);

        if ($order->status->is(OrderStatus::INVITED)) {
            if ($order->batch->getAvailableStock() < 1) {
                Session::flash('error', 'Stock is not available.');

                $this->redirectRoute(name: 'orders.list');
            } else {
                $fee = $this->generateUniqueAmount();

                /** @var User $user */
                $user = User::findOrFail($order->user->id);
                $user->name = $this->form->name;
                $user->markEmailAsVerified();
                $user->status = UserStatus::COMPLETED;

                $user->profile->phone = $this->form->phone;
                $user->profile->instagram = $this->form->instagram ?? null;
                $user->profile->source()->associate(Source::findOrFail($this->form->source));

                $user->save();
                $user->profile->save();

                $order->name = $user->name;
                $order->phone = $user->profile->phone;
                $order->instagram = $user->profile->instagram;
                $order->source()->associate($user->profile->source);
                $order->comment = $this->form->comment ?? null;
                $order->status = OrderStatus::CONFIRMED;
                $order->confirmed_at = Carbon::now();
                $order->amount = $fee;

                $order->save();

                if (null === $order->orderItem) {
                    $item = new OrderItem();
                } else {
                    $item = OrderItem::findOrFail($order->orderItem->id);
                }

                $item->receiver_en_name = $this->form->receiverEnName;
                $item->receiver_th_name = $this->form->receiverThName;
                $item->qty = 1;
                $item->amount = 0;
                $item->gender = Gender::from($this->form->gender);
                $item->religion()->associate(Religion::find($this->form->religion));
                $item->designation()->associate(Designation::find($this->form->designation));
                $item->order()->associate($order);
                $item->identity_file = $this->form->identityFile->store('orders/identities');

                $item->save();

                if (null === $order->shipping) {
                    $shipping = new Shipping();
                } else {
                    $shipping = Shipping::findOrFail($order->shipping->id);
                }

                $shipping->order()->associate($order);
                $shipping->name = $this->form->receiverEnName;
                $shipping->phone = $this->form->receiverPhone;
                $shipping->address = $this->form->address;
                $shipping->subDistrict()->associate(SubDistrict::findOrFail($this->form->subDistrict));
                $shipping->fee = $fee;

                $shipping->save();

                if (!$order->payment) {
                    $payment = new Payment();
                    $payment->order()->associate($order);
                    $payment->expired_at = Carbon::now()->addMinutes(30);
                    $payment->status = PaymentStatus::PAID;
                    $payment->paid_at = Carbon::now();
                    $payment->receipt_file = $this->form->receiptFile->store('payments/receipts');

                    $payment->save();
                }

                if (Storage::directoryExists('livewire-tmp')) {
                    Storage::deleteDirectory('livewire-tmp');
                }

                event(new OrderInvitationConfirmed($order));

                Session::flash('message', sprintf('Order %s has been successfully created.', $order->code));

                $this->redirectRoute(name: 'orders.payment.success', parameters: ['order' => $order->id]);
            }
        } else {
            Session::flash('error', 'Unable to place orders.');

            $this->redirectRoute(name: 'orders.list');
        }
    }

    protected function generateUniqueAmount(): float
    {
        $lastOrder = Order::where('batch_id', $this->order->batch->id)->orderBy('code', 'desc')->first();

        if ($lastOrder) {
            $lastAmount = $lastOrder->amount;
            $nextAmount = $lastAmount + 0.01;
        } else {
            $nextAmount = 100.00; // Initial amount
        }

        return round($nextAmount, 2);
    }
}

?>

<section>
    <form wire:submit="save">
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('User Information') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __("Fill in your user data as a booking requirement.") }}
            </p>
        </header>

        <div class="mt-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="name" :value="__('Full Name')" class="required"/>
                    <x-text-input wire:model="form.name" id="name" name="name" type="text"
                                  class="mt-1 block w-full"
                                  autofocus autocomplete="name"
                                  placeholder="Please enter your full name"/>
                    <x-input-error class="mt-2" :messages="$errors->get('form.name')"/>
                </div>
                <div>
                    <x-input-label for="phone" :value="__('Mobile No.')" class="required"/>
                    <x-text-input wire:model="form.phone" id="phone" name="phone" type="text" class="mt-1 block w-full"
                                  autofocus autocomplete="phone"
                                  placeholder="Please enter your mobile phone number"/>
                    <x-input-error class="mt-2" :messages="$errors->get('form.phone')"/>
                </div>
                <div>
                    <x-input-label for="instagram" :value="__('Instagram (Optional)')"/>
                    <div class="flex mt-1 w-full">
                  <span
                      class="inline-flex items-center px-3 text-sm text-gray-900 border rounded-e-0 border-gray-300 rounded-s-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                    @
                  </span>
                        <x-text-input wire:model="form.instagram" id="instagram" name="instagram" type="text"
                                      class="block w-full rounded-none rounded-e-lg border text-gray-900 flex-1 min-w-0 text-sm border-gray-300 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                      autofocus autocomplete="instagram"
                                      placeholder="Please enter your username instagram account"/>
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('form.instagram')"/>
                </div>
                <div>
                    <x-input-label for="source" :value="__('How do you know us')" class="required"/>
                    <x-select-input wire:model="form.source" id="source" name="source" class="mt-1 block w-full"
                                    :options="$sources"
                                    autofocus/>
                    <x-input-error class="mt-2" :messages="$errors->get('form.source')"/>
                </div>
            </div>
        </div>

        <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">

        <header>
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Booking Information') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __("To proceed with this booking process, please correctly complete the following details so that we can verify your order.") }}
            </p>
        </header>

        <div class="mt-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="receiver_en_name" :value="__('Receiver Name in English')" class="required"/>
                    <x-text-input wire:model="form.receiverEnName" id="receiver_en_name" name="receiver_en_name"
                                  type="text"
                                  class="mt-1 block w-full"
                                  autofocus autocomplete="receiver_en_name"
                                  placeholder="Please enter receiver name in english"/>
                    <x-input-error class="mt-2" :messages="$errors->get('form.receiverEnName')"/>
                </div>
                <div>
                    <x-input-label for="receiver_th_name" :value="__('Receiver Name in Thai')" class="required"/>
                    <x-text-input wire:model="form.receiverThName" id="receiver_th_name" name="receiver_th_name"
                                  type="text"
                                  class="mt-1 block w-full"
                                  autofocus autocomplete="receiver_th_name"
                                  placeholder="Please enter receiver name in thai"/>
                    <x-input-error class="mt-2" :messages="$errors->get('form.receiverThName')"/>
                </div>
                <div>
                    <x-input-label for="designation" :value="__('Order For')" class="required"/>
                    <x-select-input wire:model="form.designation" id="designation" name="designation"
                                    class="mt-1 block w-full"
                                    :options="$designations"
                                    autofocus/>
                    <x-input-error class="mt-2" :messages="$errors->get('form.designation')"/>
                </div>
                <div>
                    <x-input-label for="gender" :value="__('Gender')" class="required"/>
                    <x-select-input wire:model="form.gender" id="gender" name="gender"
                                    class="mt-1 block w-full"
                                    :options="$genders"
                                    autofocus/>
                    <x-input-error class="mt-2" :messages="$errors->get('form.gender')"/>
                </div>
                <div>
                    <x-input-label for="religion" :value="__('Religion')" class="required"/>
                    <x-select-input wire:model="form.religion" id="religion" name="religion" class="mt-1 block w-full"
                                    :options="$religions"
                                    autofocus/>
                    <x-input-error class="mt-2" :messages="$errors->get('form.religion')"/>
                </div>
                <div>
                    <x-input-label for="identity_file" :value="__('Receiver Thai ID')" class="required"/>
                    <input wire:model="form.identityFile"
                           class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                           aria-describedby="file_input_help" id="file_input" type="file"
                           accept="image/png, image/jpg, image/jpeg">
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">jpeg, jpg, or png
                        (max.
                        5MB).</p>
                    <x-input-error class="mt-2" :messages="$errors->get('form.identityFile')"/>
                </div>
                <div>
                    <x-input-label for="comment" :value="__('Comment (Optional)')"/>
                    <x-text-area wire:model="form.comment" id="comment" name="comment" class="mt-1 block w-full"
                                 autofocus autocomplete="comment"/>
                    <x-input-error class="mt-2" :messages="$errors->get('form.comment')"/>
                </div>
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

        <div class="mt-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="receiver_phone" :value="__('Receiver Mobile No.')" class="required"/>
                    <x-text-input wire:model="form.receiverPhone" id="receiver_phone" name="receiver_phone" type="text"
                                  class="mt-1 block w-full"
                                  autofocus autocomplete="receiver_phone"
                                  placeholder="Please enter receiver mobile number"/>
                    <x-input-error class="mt-2" :messages="$errors->get('form.receiverPhone')"/>
                </div>
                <div>
                    <x-input-label for="address" :value="__('Soi (Street Address)')" class="required"/>
                    <x-text-area wire:model="form.address" id="address" name="address" class="mt-1 block w-full"
                                 autofocus autocomplete="address"/>
                    <x-input-error class="mt-2" :messages="$errors->get('form.address')"/>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="region" :value="__('Region')" class="required"/>
                    <x-select-input wire:model.live="form.region" wire:change="getCitiesByRegion" id="region"
                                    name="region"
                                    class="mt-1 block w-full"
                                    :options="$regions"
                                    autofocus/>
                    <x-input-error class="mt-2" :messages="$errors->get('form.region')"/>
                </div>
                <div>
                    <x-input-label for="city" :value="__('Province')" class="required"/>
                    <x-select-input wire:model.live="form.city" wire:key="{{ $form->region }}"
                                    wire:change="getDistrictsByCity"
                                    id="city" name="city" class="mt-1 block w-full"
                                    :options="$cities"
                                    autofocus/>
                    <x-input-error class="mt-2" :messages="$errors->get('form.city')"/>
                </div>
                <div>
                    <x-input-label for="district" :value="__('Amphoe (District)')" class="required"/>
                    <x-select-input wire:model.live="form.district" wire:key="{{ $form->city }}"
                                    wire:change="getSubDistrictsByDistrict" id="district" name="district"
                                    class="mt-1 block w-full"
                                    :options="$districts"
                                    autofocus/>
                    <x-input-error class="mt-2" :messages="$errors->get('form.district')"/>
                </div>
                <div>
                    <x-input-label for="subDistrict" :value="__('Tambon (Sub-District)')" class="required"/>
                    <x-select-input wire:model="form.subDistrict" wire:key="{{ $form->district }}"
                                    wire:change="selectSubDistrict"
                                    id="subDistrict" name="subDistrict"
                                    class="mt-1 block w-full"
                                    :options="$subDistricts"
                                    autofocus/>
                    <x-input-error class="mt-2" :messages="$errors->get('form.subDistrict')"/>
                </div>
{{--                <div>--}}
{{--                    <x-input-label for="delivery_fee" :value="__('Delivery & Service Fee')" class="required"/>--}}
{{--                    <div class="flex mt-1 w-full">--}}
{{--                  <span--}}
{{--                      class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border rounded-e-0 border-gray-300 rounded-s-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">--}}
{{--                    THB--}}
{{--                  </span>--}}
{{--                        <x-text-input wire:model="form.fee" id="delivery_fee" name="delivery_fee" type="text"--}}
{{--                                      class="block w-full rounded-none rounded-e-lg bg-gray-50 border text-gray-900 flex-1 min-w-0 text-sm border-gray-300 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"--}}
{{--                                      autofocus autocomplete="delivery_fee" readonly/>--}}
{{--                    </div>--}}
{{--                    <x-input-error class="mt-2" :messages="$errors->get('form.fee')"/>--}}
{{--                </div>--}}
                <div>
                    <x-input-label for="zip_code" :value="__('Zip Code')" class="required"/>
                    <x-text-input wire:model="form.zipCode" id="zip_code" name="zip_code" type="text"
                                  class="mt-1 block w-full bg-gray-50 border text-gray-900 flex-1 min-w-0 text-sm border-gray-300 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                  autofocus autocomplete="zip_code" readonly/>
                    <x-input-error class="mt-2" :messages="$errors->get('form.zipCode')"/>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="qty" :value="__('Quantity')"/>
                    <p class="text-sm mt-1 text-gray-500">1 pcs ThaiQuran (750gr)</p>
                </div>
                <div>
                    <x-input-label for="price" :value="__('Price')"/>
                    <span
                        class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">FREE</span>
                </div>
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

        <div class="mt-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
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
                    </div>
                    <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <x-input-label for="receipt_file" :value="__('Upload Payment Receipt')" class="required"/>
                            <input wire:model="form.receiptFile"
                                   class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                                   aria-describedby="file_input_help" id="receipt_file" type="file"
                                   accept="image/png, image/jpg, image/jpeg">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">jpeg, jpg,
                                or png
                                (max.
                                5MB).</p>
                            <x-input-error class="mt-2" :messages="$errors->get('form.receiptFile')"/>
                        </div>
                    </div>
                </div>
                <div>
                    <figure class="max-w-lg">
                        <img class="h-auto max-w-sm mx-auto rounded-lg"
                             src="{{ asset('images/qr-payment.png') }}" alt="">
                    </figure>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Create Booking Now') }}</x-primary-button>
        </div>
    </form>
</section>
