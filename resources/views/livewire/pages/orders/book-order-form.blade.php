<?php

use App\Event\Order\OrderCreated;
use App\Livewire\Forms\Order\CreateOrderForm;
use App\Models\Batch;
use App\Models\Designation;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Religion;
use App\Models\Source;
use App\Models\User;
use App\ValueObject\BatchStatus;
use App\ValueObject\Gender;
use App\ValueObject\OrderStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use function Livewire\Volt\{state};

new class extends Component {
    use WithFileUploads;

    public array $sources = [];
    public array $designations = [];
    public array $genders = [];
    public array $religions = [];

    public CreateOrderForm $form;

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->form->email = $user->email;
        $this->form->name = $user->name;
        $this->form->phone = $user->profile->phone;
        $this->form->instagram = $user->profile->instagram;
        $this->sources = Source::get()->map(fn($item) => ['value' => $item->id, 'label' => $item->name])->toArray();
        $this->designations =
            Designation::get()->map(fn($item) => ['value' => $item->id, 'label' => $item->name])->toArray();
        $this->genders = Gender::getOptions();
        $this->religions = Religion::get()->map(fn($item) => ['value' => $item->id, 'label' => $item->name])->toArray();
    }

    public function save(): void
    {
        $this->form->validate();

        /** @var Batch $batch */
        $batch = Batch::where('status', BatchStatus::PUBLISHED)->first();

        if (!$batch || $batch->getAvailableStock() <= 0) {
            Session::flash('error', 'Unable to place orders due to out-of-stock.');

            $this->redirectRoute('orders.book');
        } else {
            $order =
                new Order(
                    $this->form->except(['source', 'receiverEnName', 'receiverThName', 'designation', 'gender', 'religion'])
                );
            $order->user()->associate(Auth::user());
            $order->batch()->associate($batch);
            $order->source()->associate(Source::find($this->form->source));
            $order->status = OrderStatus::DRAFT;
            $order->code = $this->createUniqueOrderCode();
            $order->qty = 1;
            $order->amount = 0;

            $order->save();

            $item = new OrderItem();
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

            Storage::deleteDirectory('livewire-tmp');

            event(new OrderCreated($order));

            Session::flash('message', sprintf('Order %s has been successfully created.', $order->code));

            $this->redirectRoute(name: 'orders.delivery', parameters: ['order' => $order->id]);
        }
    }

    private function generateCode(): string
    {
        $date = now()->format('ymd');
        $seq =
            sprintf(
                "%'.04d",
                Order::whereYear('created_at', date('Y'))
                     ->whereMonth('created_at', date('n'))
                     ->whereDay('created_at', date('d'))
                     ->count() + 1
            );

        return $date . $seq;
    }

    private function createUniqueOrderCode(): string
    {
        return DB::transaction(
            function () {
                DB::table('orders')->lockForUpdate()->get();

                return $this->generateCode();
            }
        );
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

    <form wire:submit="save" class="mt-6 space-y-6">
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
                       aria-describedby="file_input_help" id="file_input" type="file"
                       accept="image/png, image/jpg, image/jpeg">
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">jpeg, jpg, or png (max.
                    2MB).</p>
                <x-input-error class="mt-2" :messages="$errors->get('form.identityFile')"/>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save And Continue') }}</x-primary-button>
        </div>
    </form>
</section>
