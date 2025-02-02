<?php

use App\Event\Order\OrderUpdated;
use App\Livewire\Forms\Order\UpdateOrderForm;
use App\Models\Designation;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Religion;
use App\Models\Source;
use App\ValueObject\Gender;
use App\ValueObject\OrderStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use function Livewire\Volt\{state};

new class extends Component {
    use WithFileUploads;

    public Order $order;
    public array $designations = [];
    public array $genders = [];
    public array $religions = [];
    public string $receiverIdentityFile;

    public UpdateOrderForm $form;

    public function mount(Request $request): void
    {
        $this->order = Order::where('id', $request->route('order'))->where('status', OrderStatus::DRAFT)->first();
        $this->designations =
            Designation::get()->map(fn($item) => ['value' => $item->id, 'label' => $item->name])->toArray();
        $this->genders = Gender::getOptions();
        $this->religions = Religion::get()->map(fn($item) => ['value' => $item->id, 'label' => $item->name])->toArray();
        $this->receiverIdentityFile = Storage::url($this->order->orderItem->identity_file);

        $this->form->comment = $this->order->comment;
        $this->form->receiverEnName = $this->order->orderItem->receiver_en_name;
        $this->form->receiverThName = $this->order->orderItem->receiver_th_name;
        $this->form->designation = $this->order->orderItem->designation->id;
        $this->form->gender = $this->order->orderItem->gender->value;
        $this->form->religion = $this->order->orderItem->religion->id;
    }

    public function update(): void
    {
        $this->form->validate();

        $order = Order::findOrFail($this->order->id);
        $order->comment = $this->form->comment ?? null;

        $order->save();

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

            //Storage::deleteDirectory('livewire-tmp');
        }

        $item->save();

        event(new OrderUpdated($order));

        Session::flash('message', sprintf('Order %s has been successfully updated.', $order->code));

        $this->redirectRoute(name: 'orders.delivery', parameters: ['order' => $order->id]);
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

    <form wire:submit="update" class="mt-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="receiver_en_name" :value="__('Receiver Name in English')" class="required"/>
                <x-text-input wire:model="form.receiverEnName" id="receiver_en_name" name="receiver_en_name" type="text"
                              class="mt-1 block w-full"
                              autofocus autocomplete="receiver_en_name"
                              placeholder="Please enter receiver name in english"/>
                <x-input-error class="mt-2" :messages="$errors->get('form.receiverEnName')"/>
            </div>
            <div>
                <x-input-label for="receiver_th_name" :value="__('Receiver Name in Thai')" class="required"/>
                <x-text-input wire:model="form.receiverThName" id="receiver_th_name" name="receiver_th_name" type="text"
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
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">jpeg, jpg, or png (max.
                    5MB).</p>
                <x-input-error class="mt-2" :messages="$errors->get('form.identityFile')"/>
            </div>
            <div>
                <figure class="max-w-lg">
                    <img class="h-auto max-w-sm mx-auto rounded-lg"
                         src="{{ $receiverIdentityFile }}" alt="">
                </figure>
            </div>
            <div>
                <x-input-label for="comment" :value="__('Comment (Optional)')"/>
                <x-text-area wire:model="form.comment" id="comment" name="comment" class="mt-1 block w-full"
                             autofocus autocomplete="comment"/>
                <x-input-error class="mt-2" :messages="$errors->get('form.comment')"/>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save And Continue') }}</x-primary-button>
        </div>
    </form>
</section>
