<?php

use App\Models\Order;
use App\ValueObject\OrderStatus;
use Illuminate\Http\Request;
use Livewire\Volt\Component;
use function Livewire\Volt\{state};

new class extends Component {
    public Order $order;
    public array $status = [];

    public function mount(Request $request): void
    {
        $this->order = Order::findOrFail($request->route('order'));

        if ($this->order->status->is(OrderStatus::CONFIRMED) ||
            $this->order->status->is(OrderStatus::VERIFIED) ||
            $this->order->status->is(OrderStatus::COMPLETED)) {
            $this->status[] = [
                'date'    => $this->order->confirmed_at?->format('d F Y H:i:s') ?? null,
                'message' => 'Booking has been received - waiting for ID & payment verification',
            ];
        }

        if ($this->order->status->is(OrderStatus::VERIFIED) || $this->order->status->is(OrderStatus::COMPLETED)) {
            $this->status[] = [
                'date'    => $this->order->verified_at?->format('d F Y H:i:s') ?? null,
                'message' => 'Booking has been verified - waiting for delivery',
            ];
        }

        if ($this->order->status->is(OrderStatus::COMPLETED)) {
            $this->status[] = [
                'date'    => $this->order->completed_at?->format('d F Y H:i:s') ?? null,
                'message' => 'Order has been delivered - Thai Post Track Code ' . $this->order->shipping->tracking_code,
            ];
        }
    }

    public function redirectToOrderDetail(): void
    {
        $this->redirectRoute(name: 'orders.detail', parameters: ['order' => $this->order->id]);
    }
};

?>

<div>
    <div class="text-center">
        <h1 class="mb-4 text-xl font-semibold leading-none tracking-tight text-gray-900 md:text-2xl lg:text-3xl dark:text-white">
            #{{ $order->code }}</h1>
        <p class="font-semibold text-lg">Thank You!</p>
        <p class="px-6"><span
                class="font-semibold">ThaiQuran Team will need 7 Working Days to process the order.</span><br/>Please be
            patient! Here is your current status.</p>
        <div class="p-6">
            <div class="relative overflow-x-auto sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3 bg-gray-50 dark:bg-gray-800">
                            Date
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Status
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($status as $item)
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap bg-gray-50 dark:text-white dark:bg-gray-800">
                                {{ $item['date'] }}
                            </th>
                            <td class="px-6 py-4 text-gray-900">
                                {{ $item['message'] }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @if($order->shipping->tracking_code)
            <p class="my-5 text-sm text-gray-500">Copy this parcel code to track parcel in ThaiPost website or click
                this
                link icon</p>
            <a href="https://track.thailandpost.co.th/?trackNumber={{ $order->shipping->tracking_code }}" target="_blank" class="hover:underline text-blue-700 font-semibold">{{ $order->shipping->tracking_code }}</a>
        @endif
        <div class="mt-5">
            <x-primary-button wire:click="redirectToOrderDetail">
                {{ __('Back to Order') }}
            </x-primary-button>
        </div>
    </div>
</div>
