<div>
    <div class="flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between pb-4">
        <x-primary-button x-data=""
                          x-on:click.prevent="$dispatch('open-modal', 'confirm-invitation-form')">
            {{ __('Invite New Order') }}
        </x-primary-button>
    </div>
    <div class="relative overflow-x-auto sm:rounded-lg">
        <div class="flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between pb-4">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 rtl:inset-r-0 rtl:right-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>
                </div>
                <input type="text" wire:model.live="search" id="table-search" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search for items">
            </div>
            <div>
                <select id="batch" wire:model.live="searchBatch" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option selected>Choose a batch</option>
                    @foreach($batches as $batch)
                        <option value="{{ $batch->id }}">Batch {{ $batch->number }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3 text-center">
                    Order Code
                </th>
                <th scope="col" class="px-6 py-3 text-center">
                    Order Date
                </th>
                <th scope="col" class="px-6 py-3 text-center">
                    Batch
                </th>
                <th scope="col" class="px-6 py-3 text-center">
                    Email
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
                        <a href="{{ route('order.invited', ['order' => $order->id]) }}" class="font-medium hover:underline cursor-pointer">#{{ $order->code }}</a>
                    </th>
                    <td class="px-6 py-4 text-center">
                        {{ $order->created_at->format('d-m-Y H:i:s') }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        {{ $order->batch->number }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        {{ $order->email }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        {{ $order->user_order_sequence }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('order.invited', ['order' => $order->id]) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline cursor-pointer">View Detail</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="pt-4">
            {{ $orders->links() }}
        </div>
    </div>

    <x-modal name="confirm-invitation-form" :show="$errors->isNotEmpty()" focusable>
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Invite New Order
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm h-8 w-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="select-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="p-4 md:p-5">
                <p class="text-gray-500 dark:text-gray-400 mb-4">Select the type of invitation you want:</p>
                <ul class="space-y-4 mb-4">
                    <li>
                        <a wire:click="redirectToInviteOrderPage" class="inline-flex items-center justify-between w-full p-5 text-gray-900 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-500 dark:peer-checked:text-blue-500 peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-900 hover:bg-gray-100 dark:text-white dark:bg-gray-600 dark:hover:bg-gray-500">
                            <div class="block">
                                <div class="w-full text-lg font-semibold">New Order</div>
                                <div class="w-full text-gray-500 dark:text-gray-400">Invite new order with fresh data. This feature is usually for new users who want to be invited to place an order.</div>
                            </div>
                            <svg class="w-4 h-4 ms-3 rtl:rotate-180 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/></svg>
                        </a>
                    </li>
                    <li>
                        <a wire:click="redirectToInviteExistingOrderPage" class="inline-flex items-center justify-between w-full p-5 text-gray-900 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-500 dark:peer-checked:text-blue-500 peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-900 hover:bg-gray-100 dark:text-white dark:bg-gray-600 dark:hover:bg-gray-500">
                            <div class="block">
                                <div class="w-full text-lg font-semibold">Existing Order</div>
                                <div class="w-full text-gray-500 dark:text-gray-400">Invite new order with existing data, this will duplicate existing data to new data. This feature is usually for users who already have a previous order.</div>
                            </div>
                            <svg class="w-4 h-4 ms-3 rtl:rotate-180 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/></svg>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </x-modal>
</div>
