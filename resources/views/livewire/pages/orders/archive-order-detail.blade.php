<?php

use App\Models\Order;
use App\ValueObject\OrderStatus;
use App\ValueObject\PaymentStatus;
use Illuminate\Http\Request;
use Livewire\Volt\Component;
use function Livewire\Volt\{state};

new class extends Component {
    public Order|null $order = null;

    public function mount(Request $request): void
    {
        $this->order = Order::whereIn('status', [OrderStatus::DRAFT, OrderStatus::PENDING, OrderStatus::CANCELED, OrderStatus::REJECTED, OrderStatus::REVISED])
                            ->where('id', $request->route('order'))
                            ->first();
    }
};

?>

<section>
    <header>
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Order #:code', ['code' => $order->code]) }}
                </h2>
            </div>
            <div>
                <span
                    class="{{ $order->status->is(OrderStatus::CANCELED) || $order->status->is(OrderStatus::REJECTED) ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' }} text-xs font-medium me-2 px-2.5 py-0.5 rounded-full">
                    {{ $order->status }}
                </span>
            </div>
        </div>
    </header>

    <div class="mt-10">
        @if($order->reason)
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400"
                 role="alert">
                <span class="font-medium">{{ $order->reason }}</span>
            </div>
        @endif
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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-9">
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

    @if($order->payment)
        <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Payment') }}
            </h2>
        </header>

        <div class="mt-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                                class="{{ $order->payment->status->is(PaymentStatus::PAID) ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }} text-xs font-medium me-2 px-2.5 py-0.5 rounded-full">{{ $order->payment->status }}</span>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Paid At</p>
                            <p class="pt-1 text-sm">{{ $order->payment->paid_at?->format('d-m-Y H:i:s') ?? '-' }}</p>
                        </div>
                    </div>
                </div>
                <div>
                    <figure class="max-w-lg">
                        <img class="h-auto max-w-sm mx-auto rounded-lg"
                             src="{{ $order->payment->receipt_file ? Storage::url($order->payment->receipt_file) : asset('images/image-default.jpg') }}"
                             alt="">
                        <figcaption class="mt-2 text-sm text-center text-gray-500 dark:text-gray-400">Payment Receipt
                            File
                        </figcaption>
                    </figure>
                </div>
            </div>
        </div>
    @endif

    @if($order->shipping)
        <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Shipping') }}
            </h2>
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
                            <p class="text-sm text-gray-500">Receiver Mobile No.</p>
                            <p class="pt-1 text-sm">{{ $order->shipping->phone }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Address</p>
                            <p class="pt-1 text-sm">{{ $order->shipping->address }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Region</p>
                            <p class="pt-1 text-sm">{{ $order->shipping->subDistrict->district->city->region->th_name }}
                                ({{ $order->shipping->subDistrict->district->city->region->en_name }})</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Province</p>
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
                            <p class="pt-1 text-sm">
                                @if($order->shipping->tracking_code)
                                    <a href="https://track.thailandpost.co.th/?trackNumber={{ $order->shipping->tracking_code }}"
                                       target="_blank" class="hover:underline">
                                        {{ $order->shipping->tracking_code ?? '-' }}
                                    </a>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</section>
