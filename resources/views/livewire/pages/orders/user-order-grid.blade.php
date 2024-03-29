@php use App\ValueObject\OrderStatus; @endphp
<div>
    <div class="relative overflow-x-auto sm:rounded-lg">
        <div class="flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between pb-4">
            <div class="relative">
                <div
                    class="absolute inset-y-0 left-0 rtl:inset-r-0 rtl:right-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="currentColor"
                         viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                              d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                              clip-rule="evenodd"></path>
                    </svg>
                </div>
                <input type="text" wire:model.live="searchKeyword" id="table-search"
                       class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                       placeholder="Search for items">
            </div>
            <div class="flex justify-end items-center">
                <div>
                    <select id="batch" wire:model.live="searchBatch"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option selected>Choose a batch</option>
                        @foreach($batches as $batch)
                            <option value="{{ $batch->id }}">Batch {{ $batch->number }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="ml-2">
                    <select id="status" wire:model.live="searchStatus" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option selected>Choose a status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3 text-center">
                    Code
                </th>
                <th scope="col" class="px-6 py-3 text-center">
                    Receiver Name
                </th>
                <th scope="col" class="px-6 py-3 text-center">
                    Batch
                </th>
                <th scope="col" class="px-6 py-3 text-center">
                    Qty
                </th>
                <th scope="col" class="px-6 py-3 text-center">
                    Status
                </th>
                <th scope="col" class="px-6 py-3 text-center">
                    Created At
                </th>
                <th scope="col" class="px-6 py-3 text-center">
                    Action
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($orders as $order)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <th scope="row" class="px-6 py-4 font-medium text-center  text-gray-900 whitespace-nowrap dark:text-white">
                        @if($order->status->is(OrderStatus::DRAFT))
                            <a href="{{ route('orders.delivery', ['order' => $order->id]) }}"
                               class="font-medium hover:underline cursor-pointer">#{{ $order->code }}</a>
                        @elseif($order->status->is(OrderStatus::PENDING))
                            <a href="{{ route('orders.payment', ['order' => $order->id]) }}"
                               class="font-medium hover:underline cursor-pointer">#{{ $order->code }}</a>
                        @else
                            <a href="{{ route('orders.detail', ['order' => $order->id]) }}"
                               class="font-medium hover:underline cursor-pointer">#{{ $order->code }}</a>
                        @endif
                    </th>
                    <td class="px-6 py-4 text-center">
                        {{ $order->orderItem?->receiver_en_name ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        {{ $order->batch->number }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        {{ $order->qty }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span
                            class="{{ $order->status->is(OrderStatus::CANCELED) || $order->status->is(OrderStatus::REJECTED) ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' }} text-xs font-medium me-2 px-2.5 py-0.5 rounded-full">{{ $order->status }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        {{ $order->created_at->format('d-m-Y H:i:s') }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($order->status->is(OrderStatus::DRAFT))
                            <a href="{{ route('orders.delivery', ['order' => $order->id]) }}"
                               class="font-medium text-blue-600 dark:text-blue-500 hover:underline cursor-pointer">Complete Delivery</a>
                        @elseif($order->status->is(OrderStatus::PENDING))
                            <a href="{{ route('orders.payment', ['order' => $order->id]) }}"
                               class="font-medium text-blue-600 dark:text-blue-500 hover:underline cursor-pointer">Confirm Payment</a>
                        @else
                            <a href="{{ route('orders.detail', ['order' => $order->id]) }}"
                               class="font-medium text-blue-600 dark:text-blue-500 hover:underline cursor-pointer">View Detail</a>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="pt-4">
            {{ $orders->links() }}
        </div>
    </div>
</div>
