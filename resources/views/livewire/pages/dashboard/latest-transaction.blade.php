<?php

use App\Models\Order;
use App\ValueObject\OrderStatus;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Volt\Component;
use function Livewire\Volt\{state};

new class extends Component {
    public Collection $orders;

    public function mount(): void
    {
        $this->orders = Order::where('orders.status', OrderStatus::CONFIRMED)
                             ->orderBy('orders.confirmed_at')
                             ->take(10)
                             ->get();
    }
};

?>

<div>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <caption
                class="p-5 text-lg font-semibold text-left rtl:text-right text-gray-900 bg-white dark:text-white dark:bg-gray-800">
                Last 10 Transactions
                <p class="mt-1 text-sm font-normal text-gray-500 dark:text-gray-400">Here is a list of the last 10 transactions that must be verified.</p>
            </caption>
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">
                    Transaction
                </th>
                <th scope="col" class="px-6 py-3">
                    Date & Time
                </th>
                <th scope="col" class="px-6 py-3">
                    Amount
                </th>
                <th scope="col" class="px-6 py-3">
                    Name
                </th>
                <th scope="col" class="px-6 py-3">
                    Action
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($orders as $order)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        Payment order #{{ $order->code }}
                    </th>
                    <td class="px-6 py-4">
                        {{ $order->confirmed_at->format('F d, Y') }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $order->amount }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $order->name }}
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('order.verify', ['order' => $order->id]) }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-xs px-3 py-2 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            Verify
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

</div>
