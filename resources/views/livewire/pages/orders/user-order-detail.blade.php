<?php

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Component;
use function Livewire\Volt\{state};

new class extends Component {
    public Order|null $order = null;

    public function mount(Request $request): void
    {
        $this->order = Order::findOrFail($request->route('order'));
    }

    public function redirectToTrackingOrder(): void
    {
        $this->redirectRoute(name: 'orders.tracking.status', parameters: ['order' => $this->order->id]);
    }
};

?>

<section>
    <header>
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Order #:code', ['code' => $order->code]) }}
            </h2>
            <x-primary-button wire:click="redirectToTrackingOrder">
                {{ __('Check Status') }}
            </x-primary-button>
        </div>
    </header>

    <div class="mt-10">
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
                <div class="grid grid-cols-1 gap-4 mt-9">
                    <div>
                        <p class="text-sm text-gray-500">Comment</p>
                        <p class="pt-1 text-sm">{{ $order->comment ?? '-' }}</p>
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
                         src="{{ $order->orderItem->identity_file ? Storage::url($order->orderItem->identity_file) : asset('images/image-default.jpg') }}" alt="">
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
                         src="{{ $order->payment->receipt_file ? Storage::url($order->payment->receipt_file) : asset('images/image-default.jpg') }}" alt="">
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
</section>