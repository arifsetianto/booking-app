<?php

use App\Event\Order\OrderRejected;
use App\Event\Order\OrderRevised;
use App\Event\Order\OrderVerified;
use App\Livewire\Forms\Order\RejectOrderForm;
use App\Livewire\Forms\Order\ReviseOrderForm;
use App\Models\Order;
use App\ValueObject\OrderStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Livewire\Volt\Component;
use function Livewire\Volt\{state};

new class extends Component {
    public Order|null $order = null;
    public RejectOrderForm $form;
    public ReviseOrderForm $reviseOrderForm;

    public function mount(Request $request): void
    {
        $this->order = Order::whereIn('status', [OrderStatus::CONFIRMED])
                            ->where('id', $request->route('order'))
                            ->first();
    }

    public function rejectOrder(): void
    {
        $this->form->validate();

        $this->order->status = OrderStatus::REJECTED;
        $this->order->rejected_at = Carbon::now();
        $this->order->reason = $this->form->reason;

        $this->order->save();

        event(new OrderRejected($this->order));

        Session::flash('message', sprintf('Order #%s has been rejected.', $this->order->code));

        $this->redirectRoute('order.list-incoming');
    }

    public function verifyOrder(): void
    {
        $this->order->status = OrderStatus::VERIFIED;
        $this->order->verified_at = Carbon::now();

        $this->order->save();

        event(new OrderVerified($this->order));

        Session::flash('message', sprintf('Order #%s has been verified.', $this->order->code));

        $this->redirectRoute('order.list-incoming');
    }

    public function reviseOrder(): void
    {
        $this->reviseOrderForm->validate();

        $this->order->status = OrderStatus::REVISED;
        $this->order->revised_at = Carbon::now();
        $this->order->reason = $this->reviseOrderForm->reason;

        $this->order->save();

        event(new OrderRevised($this->order));

        Session::flash('message', sprintf('Request update order #%s has been sent.', $this->order->code));

        $this->redirectRoute('order.list-incoming');
    }
};

?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Order #:code', ['code' => $order->code]) }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("The following are the details of the order, please check the correctness of the data before you select confirm order.") }}
        </p>
    </header>

    <div class="mt-10">
        @if($order->comment)
            <div id="alert-additional-content-5"
                 class="p-4 mb-5 rounded-lg bg-gray-100 dark:border-gray-600 dark:bg-gray-800"
                 role="alert">
                <div class="flex items-center">
                    <svg class="flex-shrink-0 w-4 h-4 me-2 dark:text-gray-300" aria-hidden="true"
                         xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                    </svg>
                    <span class="sr-only">Info</span>
                    <h3 class="text-base font-medium text-gray-800 dark:text-gray-300">Comments</h3>
                </div>
                <div class="mt-2 mb-4 text-sm text-gray-800 dark:text-gray-300">
                    {{ $order->comment }}
                </div>
            </div>
        @endif
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Full Name</p>
                        <p class="pt-1 text-sm">{{ $order->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Email</p>
                        <p class="pt-1 text-sm">{{ $order->email }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Phone</p>
                        <p class="pt-1 text-sm">{{ $order->phone }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Instagram</p>
                        <p class="pt-1 text-sm">{{ $order->instagram }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">How do you know us</p>
                        <p class="pt-1 text-sm">{{ $order->source->name }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-9">
                    <div>
                        <p class="text-sm text-gray-500">Receiver Name (in English)</p>
                        <p class="pt-1 text-sm">{{ $order->orderItem->receiver_en_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Receiver Name (in Thai)</p>
                        <p class="pt-1 text-sm">{{ $order->orderItem->receiver_th_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Order Date</p>
                        <p class="pt-1 text-sm">{{ $order->orderItem->created_at->format('d-m-Y H:i:s') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Order For</p>
                        <p class="pt-1 text-sm">{{ $order->orderItem->designation->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Gender</p>
                        <p class="pt-1 text-sm capitalize">{{ $order->orderItem->gender }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Religion</p>
                        <p class="pt-1 text-sm">{{ $order->orderItem->religion->name }}</p>
                    </div>
                </div>
            </div>
            <div>
                <figure class="max-w-lg">
                    <img class="h-auto max-w-sm mx-auto rounded-lg"
                         src="{{ $order->orderItem->identity_file ? Storage::url($order->orderItem->identity_file) : asset('images/image-default.jpg') }}"
                         alt="">
                    <figcaption class="mt-2 text-sm text-center text-gray-500 dark:text-gray-400">Receiver Thai ID
                    </figcaption>
                </figure>
            </div>
        </div>
    </div>
    <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Payment') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("The following are the details of the payment, please check the correctness of the data before you select confirm order.") }}
        </p>
    </header>

    <div class="mt-10">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Bank Name</p>
                        <p class="pt-1 text-sm">Krungthai Bank</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Account Name</p>
                        <p class="pt-1 text-sm">Thaiquran Foundation</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Account Number</p>
                        <p class="pt-1 text-sm">819-0-47810-9</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Delivery Fee</p>
                        <p class="pt-1 text-sm">THB {{ $order->shipping->fee }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        <p class="pt-1 text-sm">
                            <span
                                class="bg-{{ $order->payment->status->getColor() }}-100 text-{{ $order->payment->status->getColor() }}-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-{{ $order->payment->status->getColor() }}-900 dark:text-{{ $order->payment->status->getColor() }}-300">{{ $order->payment->status }}</span>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Paid At</p>
                        <p class="pt-1 text-sm">{{ $order->payment->paid_at->format('d-m-Y H:i:s') }}</p>
                    </div>
                </div>
            </div>
            <div>
                <figure class="max-w-lg">
                    <img class="h-auto max-w-sm mx-auto rounded-lg"
                         src="{{ $order->payment->receipt_file ? Storage::url($order->payment->receipt_file) : asset('images/image-default.jpg') }}"
                         alt="">
                    <figcaption class="mt-2 text-sm text-center text-gray-500 dark:text-gray-400">Payment Receipt File
                    </figcaption>
                </figure>
            </div>
        </div>
    </div>
    <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Shipping') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("The following are the details of the shipment, please check the correctness of the data before you select confirm order.") }}
        </p>
    </header>

    <div class="mt-10">
        <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
            <div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Receiver Name</p>
                        <p class="pt-1 text-sm">{{ $order->shipping->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Receiver Phone</p>
                        <p class="pt-1 text-sm">{{ $order->shipping->phone }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Address</p>
                        <p class="pt-1 text-sm">{{ $order->shipping->address }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Region</p>
                        <p class="pt-1 text-sm">{{ $order->shipping->subDistrict->district->city->region->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">City</p>
                        <p class="pt-1 text-sm">{{ $order->shipping->subDistrict->district->city->th_name }}
                            ({{ $order->shipping->subDistrict->district->city->en_name }})</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">District</p>
                        <p class="pt-1 text-sm">{{ $order->shipping->subDistrict->district->th_name }}
                            ({{ $order->shipping->subDistrict->district->en_name }})</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Sub District</p>
                        <p class="pt-1 text-sm">{{ $order->shipping->subDistrict->th_name }}
                            ({{ $order->shipping->subDistrict->en_name }})</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Zip Code</p>
                        <p class="pt-1 text-sm">{{ $order->shipping->subDistrict->zip_code }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tracking Code</p>
                        <p class="pt-1 text-sm">{{ $order->shipping->tracking_code ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="flex items-center gap-4 mt-14">
        <x-primary-button x-data=""
                          x-on:click.prevent="$dispatch('open-modal', 'confirm-order-verification')">
            {{ __('Verify Order') }}
        </x-primary-button>
        <x-danger-button x-data=""
                         x-on:click.prevent="$dispatch('open-modal', 'confirm-order-rejection')">
            {{ __('Reject Order') }}
        </x-danger-button>
        <x-secondary-button x-data=""
                            x-on:click.prevent="$dispatch('open-modal', 'confirm-order-revision')">
            {{ __('Ask Customer to Edit') }}
        </x-secondary-button>
    </div>

    <x-modal name="confirm-order-verification" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="verifyOrder" class="p-6">
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
                        verify this order?</h3>
                    <x-primary-button
                        class="text-white bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                        {{ __('Yes, I\'m sure') }}
                    </x-primary-button>
                    <x-secondary-button x-on:click="$dispatch('close')"
                                        class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                        {{ __('No, Cancel') }}
                    </x-secondary-button>
                </div>
            </div>
        </form>
    </x-modal>

    <x-modal name="confirm-order-rejection" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="rejectOrder" class="p-6">

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Are you sure you want to reject this order?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Please input the reason for rejecting this order.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="reason" value="{{ __('Reason') }}" class="sr-only"/>

                <x-text-area
                    wire:model="form.reason"
                    id="reason"
                    name="reason"
                    class="mt-1 block w-full"
                />

                <x-input-error :messages="$errors->get('form.reason')" class="mt-2"/>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('Reject Order') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>

    <x-modal name="confirm-order-revision" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="reviseOrder" class="p-6">

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Are you sure you want to send request update this order to customer?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Please input the reason for update this order.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="reason" value="{{ __('Reason') }}" class="sr-only"/>

                <x-text-area
                    wire:model="reviseOrderForm.reason"
                    id="reason"
                    name="reason"
                    class="mt-1 block w-full"
                />

                <x-input-error :messages="$errors->get('reviseOrderForm.reason')" class="mt-2"/>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ms-3">
                    {{ __('Send Request Update') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
</section>
