<?php

use App\Models\Order;
use App\ValueObject\OrderStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Livewire\Volt\Component;
use function Livewire\Volt\{state};

new class extends Component {
    public Collection $orders;
    public string $orderId;

    public function mount(Request $request): void
    {
        $this->orderId = $request->route('order');
        $order = Order::findOrFail($request->route('order'));
        $this->orders = Order::where('user_id', $order->user->id)
                             ->orderBy('code')
                             ->get();
    }

    public function clickDetail(string $orderId): void
    {
        $order = Order::findOrFail($orderId);

        if ($order->status->is(OrderStatus::CONFIRMED)) {
            $this->redirectRoute(name: 'order.verify', parameters: ['order' => $order->id]);
        } elseif ($order->status->is(OrderStatus::VERIFIED)) {
            $this->redirectRoute(name: 'order.complete', parameters: ['order' => $order->id]);
        } elseif ($order->status->is(OrderStatus::COMPLETED)) {
            $this->redirectRoute(name: 'order.shipped', parameters: ['order' => $order->id]);
        } elseif ($order->status->is(OrderStatus::INVITED)) {
            $this->redirectRoute(name: 'order.invited', parameters: ['order' => $order->id]);
        } else {
            $this->redirectRoute(name: 'order.archive', parameters: ['order' => $order->id]);
        }
    }
};

?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('User Orders') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("The following is a list of orders booked by users.") }}
        </p>
    </header>

    <div class="py-5 mb-4">
        <ol class="mt-3 divide-y divider-gray-200 dark:divide-gray-700">
            @foreach($orders as $order)
                @if($order->id === $orderId)
                    <li>
                        <a wire:click="clickDetail('{{ $order->id }}')"
                           class="block px-2 py-3 bg-gray-100 hover:bg-gray-300 dark:hover:bg-gray-700 cursor-pointer">
                            <div class="flex justify-between">
                                <div class="text-gray-600 dark:text-gray-400 items-center">
                                    <div class="text-base font-normal">
                                        <span class="font-bold text-emerald-600 dark:text-white">{{ $order->user_order_sequence }}. #{{ $order->code }}</span>
                                    </div>
                                    @if($order->orderItem)
                                        <div class="text-sm font-normal">{{ $order->orderItem?->receiver_th_name }}
                                            ({{ $order->orderItem?->receiver_en_name }})
                                        </div>
                                    @endif
                                    <span class="inline-flex items-center text-xs font-normal text-gray-500 dark:text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                             class="w-2.5 h-2.5 me-1">
                                          <path fill-rule="evenodd"
                                                d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 6a.75.75 0 0 0-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 0 0 0-1.5h-3.75V6Z"
                                                clip-rule="evenodd"/>
                                        </svg>
                                        {{ $order->created_at->format('d-m-Y H:i:s') }}
                                    </span>
                                </div>
                                <div>
                                    <span
                                        class="{{ $order->status->is(OrderStatus::CANCELED) || $order->status->is(OrderStatus::REJECTED) ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' }} text-xs font-medium me-2 px-2.5 py-0.5 rounded-full">
                                        {{ $order->status }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    </li>
                @else
                    <li>
                        <a wire:click="clickDetail('{{ $order->id }}')"
                           class="block px-2 py-3 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer">
                            <div class="flex justify-between">
                                <div class="text-gray-600 dark:text-gray-400 items-center">
                                    <div class="text-base font-normal"><span class="font-medium text-gray-900 dark:text-white">{{ $order->user_order_sequence }}. #{{ $order->code }}</span>
                                    </div>
                                    @if($order->orderItem)
                                        <div class="text-sm font-normal">{{ $order->orderItem?->receiver_th_name }}
                                            ({{ $order->orderItem?->receiver_en_name }})
                                        </div>
                                    @endif
                                    <span class="inline-flex items-center text-xs font-normal text-gray-500 dark:text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                             class="w-2.5 h-2.5 me-1">
                                          <path fill-rule="evenodd"
                                                d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 6a.75.75 0 0 0-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 0 0 0-1.5h-3.75V6Z"
                                                clip-rule="evenodd"/>
                                        </svg>
                                        {{ $order->created_at->format('d-m-Y H:i:s') }}
                                    </span>
                                </div>
                                <div>
                                    <span
                                        class="{{ $order->status->is(OrderStatus::CANCELED) || $order->status->is(OrderStatus::REJECTED) ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' }} text-xs font-medium me-2 px-2.5 py-0.5 rounded-full">
                                        {{ $order->status }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    </li>
                @endif
            @endforeach
        </ol>
    </div>
</section>
