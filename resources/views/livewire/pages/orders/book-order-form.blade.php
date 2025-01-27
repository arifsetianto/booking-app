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
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use function Livewire\Volt\{state};

new class extends Component {
    use WithFileUploads;

    public array $designations = [];
    public array $genders = [];
    public array $religions = [];

    public CreateOrderForm $form;

    public function mount(): void
    {
        $this->designations =
            Designation::orderBy('number')
                       ->get()
                       ->map(fn($item) => ['value' => $item->id, 'label' => $item->name])
                       ->toArray();
        $this->genders = Gender::getOptions();
        $this->religions = Religion::get()->map(fn($item) => ['value' => $item->id, 'label' => $item->name])->toArray();
    }

    public function save(): void
    {
        $this->form->validate();

        /** @var User $user */
        $user = Auth::user();

        /** @var Batch $batch */
        $batch = Batch::where('status', BatchStatus::PUBLISHED)->first();

        if (!$batch || $batch->getAvailableStock() <= 0) {
            Session::flash('error', 'Unable to place orders due to out-of-stock.');

            $this->redirectRoute('orders.book');
        } else {
            $fileContent = $this->form->identityFile->getContent();
            $fileHash = md5($fileContent);

            // Check if the file with the same hash exists
            $existingFile = OrderItem::where('identity_file_hash', $fileHash)->first();

            if ($existingFile) {
                Session::flash('error', 'The Receiver Thai ID file has been uploaded. Please use a different file.');

                $this->redirectRoute('orders.book');
            } else {
                $userOrderCount = Order::where('user_id', $user->id)->count();
                $orderData = $this->generateOrderData($batch);
                $order =
                    new Order(
                        $this->form->except(
                            ['receiverEnName', 'receiverThName', 'designation', 'gender', 'religion', 'identityFile']
                        )
                    );
                $order->email = $user->email;
                $order->phone = $user->profile->phone;
                $order->name = $user->name;
                $order->instagram = $user->profile->instagram;
                $order->user()->associate($user);
                $order->batch()->associate($batch);
                $order->source()->associate($user->profile->source);
                $order->status = OrderStatus::DRAFT;
                $order->code = $orderData['code'];
                $order->qty = 1;
                $order->amount = $orderData['amount'];
                $order->user_order_sequence = $userOrderCount + 1;

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
                $item->identity_file_hash = $fileHash;
                $item->identity_file = $this->form->identityFile->store('orders/identities');

                $item->save();

                Storage::deleteDirectory('livewire-tmp');

                event(new OrderCreated($order));

                Session::flash('message', sprintf('Order %s has been successfully created.', $order->code));

                $this->redirectRoute(name: 'orders.delivery', parameters: ['order' => $order->id]);
            }
        }
    }

    private function generateOrderData(Batch $batch): array
    {
        return DB::transaction(function () use ($batch) {
            $date = now()->format('ymd');

            // Generate unique code
            $orderCount = Order::whereYear('created_at', now()->year)
                               ->whereMonth('created_at', now()->month)
                               ->whereDay('created_at', now()->day)
                               ->lockForUpdate() // Lock rows to avoid race conditions
                               ->count();

            $seq = sprintf("%'.04d", $orderCount + 1);
            $code = $date . $seq;

            // Generate unique amount
            $lastOrder = Order::where('batch_id', $batch->id)
                              ->lockForUpdate() // Lock rows related to this batch
                              ->orderBy('amount', 'desc')
                              ->first();

            if ($lastOrder) {
                $lastAmount = $lastOrder->amount;
                $nextAmount = $lastAmount + 0.01;
            } else {
                $nextAmount = 100.00 + 0.01; // Initial amount
            }

            $amount = round($nextAmount, 2);

            return [
                'code'   => $code,
                'amount' => $amount,
            ];
        });
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
