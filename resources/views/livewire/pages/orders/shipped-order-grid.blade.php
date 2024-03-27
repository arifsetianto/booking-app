<div>
    <div class="relative overflow-x-auto sm:rounded-lg">
        <div class="flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between pb-4">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 rtl:inset-r-0 rtl:right-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>
                </div>
                <input type="text" wire:model.live="searchKeyword" id="table-search" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search for items">
            </div>
            <div class="flex justify-end items-center">
                <div>
                    <select id="batch" wire:model.live="searchBatch" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option selected>Choose a batch</option>
                        @foreach($batches as $batch)
                            <option value="{{ $batch->id }}">Batch {{ $batch->number }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3 text-center">
                    Order
                </th>
                <th scope="col" class="px-6 py-3 text-center">
                    Tracking Code
                </th>
                <th scope="col" class="px-6 py-3 text-center">
                    Batch
                </th>
                <th scope="col" class="px-6 py-3 text-center">
                    Name
                </th>
                <th scope="col" class="px-6 py-3 text-center">
                    Contact
                </th>
                <th scope="col" class="px-6 py-3 text-center">
                    Receiver
                </th>
                <th scope="col" class="px-6 py-3 text-center">
                    Zip Code
                </th>
                <th scope="col" class="px-6 py-3 text-center">
                    Order ke
                </th>
                <th scope="col" class="px-6 py-3 text-center">
                    Action
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($orders as $order)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <th scope="row" class="px-6 py-4 font-medium text-center text-gray-900 whitespace-nowrap dark:text-white">
                        <a href="{{ route('order.shipped', ['order' => $order->id]) }}" class="font-medium hover:underline cursor-pointer">#{{ $order->code }}</a>
                    </th>
                    <td class="px-6 py-4 text-center">
                        @if($order->shipping?->tracking_code)
                            <a href="https://track.thailandpost.co.th/?trackNumber={{ $order->shipping->tracking_code }}" target="_blank" class="hover:underline">{{ $order->shipping?->tracking_code ?? '-' }}</a>
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        {{ $order->batch->number }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        {{ $order->name }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <p>{{ $order->phone }}</p>
                        <p>{{ $order->email }}</p>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <p>{{ $order->orderItem?->receiver_th_name }} ({{ $order->orderItem?->receiver_en_name }})</p>
                        <p>{{ $order->shipping?->phone }}</p>
                    </td>
                    <td class="px-6 py-4 text-center">
                        {{ $order->shipping?->subDistrict?->zip_code }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        {{ $order->user_order_sequence }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('order.shipped', ['order' => $order->id]) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline cursor-pointer">View Detail</a>
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
