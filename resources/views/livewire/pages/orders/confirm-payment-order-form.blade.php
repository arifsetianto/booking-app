<?php

use App\Event\Order\OrderConfirmed;
use App\Livewire\Forms\Order\ConfirmPaymentForm;
use App\Models\Order;
use App\Models\Payment;
use App\ValueObject\OrderStatus;
use App\ValueObject\PaymentStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use function Livewire\Volt\{state};

new class extends Component {
    use WithFileUploads;

    public int $countdownTime = 60 * 30; // Initial countdown time in seconds
    public Order $order;

    public ConfirmPaymentForm $form;

    public function mount(Request $request): void
    {
        $this->order = Order::findOrFail($request->route('order'));
        $this->countdownTime = Carbon::now()->diffInSeconds($this->order->payment->expired_at);
    }

    public function decrementCountdown(): void
    {
        $this->countdownTime--;
    }

    public function formatTime($seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds / 60) % 60);
        $seconds = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    public function fetchData(): void
    {
        $this->order = Order::findOrFail($this->order->id);
    }

    public function submit(): void
    {
        $this->form->validate();

        /** @var Order $order */
        $order = Order::findOrFail($this->order->id);
        $order->status = OrderStatus::CONFIRMED;
        $order->confirmed_at = Carbon::now();

        $order->save();

        /** @var Payment $payment */
        $payment = Payment::findOrFail($order->payment->id);
        $payment->status = PaymentStatus::PAID;
        $payment->paid_at = Carbon::now();
        $payment->receipt_file = $this->form->receiptFile->store('payments/receipts');

        $payment->save();

        Storage::deleteDirectory('livewire-tmp');

        event(new OrderConfirmed($order));

        $this->redirectRoute(name: 'orders.payment.success', parameters: ['order' => $order->id]);
    }
};

?>

<div>
    @if($order->payment->status->is(PaymentStatus::PENDING))
        <div wire:poll.1000ms="fetchData">
            <div class="text-center">
                <h1 class="mb-4 text-xl font-semibold leading-none tracking-tight text-gray-900 md:text-2xl lg:text-3xl dark:text-white">
                    Confirm your payment</h1>
                <p class="font-semibold">Time remaining: <span id="countdown">{{ $this->formatTime($countdownTime) }}</span>
                    seconds</p>
                <p class="mt-5 text-sm">Note: Please finish the payment before the time limit. You can find this page link
                    in
                    your email.</p>
            </div>
            <div class="w-full mt-10">
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
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Quantity</p>
                                <p class="pt-1 text-sm">1 pcs ThaiQuran</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Delivery & Service Fee</p>
                                <p class="pt-1 text-sm">THB 100</p>
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
                        <div class="flex items-center gap-4 mt-6">
                            <x-primary-button x-data=""
                                              x-on:click.prevent="$dispatch('open-modal', 'confirm-order-payment')">{{ __('Submit') }}</x-primary-button>
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

            <x-modal name="confirm-order-payment" :show="$errors->isNotEmpty()" focusable>
                <form wire:submit="submit" class="p-6">
                    <div class="relative bg-white rounded-lg dark:bg-gray-700">
                        <button type="button"
                                class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                x-on:click="$dispatch('close')">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                 viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                        <div class="p-4 md:p-5 text-center">
                            <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true"
                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                            </svg>
                            <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to
                                submit this payment receipt?</h3>
                            <x-primary-button
                                class="text-white bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                                {{ __('Yes, confirm') }}
                            </x-primary-button>
                            <x-secondary-button x-on:click="$dispatch('close')"
                                                class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                                {{ __('No, Cancel') }}
                            </x-secondary-button>
                        </div>
                    </div>
                </form>
            </x-modal>
        </div>
    @elseif($order->payment->status->is(PaymentStatus::PAID))
        <div class="text-center">
            <h1 class="mb-4 text-xl font-semibold leading-tight tracking-tight text-gray-900 md:text-2xl lg:text-3xl dark:text-white">
                Payment #{{ $order->code }} already paid!</h1>
        </div>
    @elseif($order->payment->status->is(PaymentStatus::EXPIRED))
        <div class="text-center">
            <h1 class="mb-4 text-xl font-semibold leading-tight tracking-tight text-gray-900 md:text-2xl lg:text-3xl dark:text-white">
                Payment #{{ $order->code }}<br/>already expired!</h1>
            <p class="mt-5 text-sm">Please book again so we can process your order, thank you.</p>
        </div>
    @endif
</div>

<script>
    setInterval(function () {
    @this.call('decrementCountdown');
    }, 1000);
</script>
