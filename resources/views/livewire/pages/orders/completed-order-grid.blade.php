<div>
    <div class="flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between pb-4">
        <div>
            <x-primary-button wire:click="exportData" class="text-center inline-flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 me-2">
                    <path fill-rule="evenodd" d="M11.47 2.47a.75.75 0 0 1 1.06 0l4.5 4.5a.75.75 0 0 1-1.06 1.06l-3.22-3.22V16.5a.75.75 0 0 1-1.5 0V4.81L8.03 8.03a.75.75 0 0 1-1.06-1.06l4.5-4.5ZM3 15.75a.75.75 0 0 1 .75.75v2.25a1.5 1.5 0 0 0 1.5 1.5h13.5a1.5 1.5 0 0 0 1.5-1.5V16.5a.75.75 0 0 1 1.5 0v2.25a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3V16.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd" />
                </svg>
                {{ __('Export Data') }}
            </x-primary-button>
            <x-danger-button class="ml-1 text-center inline-flex items-center" x-data=""
                             x-on:click.prevent="$dispatch('open-modal', 'confirm-order-import')">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 me-2">
                    <path fill-rule="evenodd" d="M12 2.25a.75.75 0 0 1 .75.75v11.69l3.22-3.22a.75.75 0 1 1 1.06 1.06l-4.5 4.5a.75.75 0 0 1-1.06 0l-4.5-4.5a.75.75 0 1 1 1.06-1.06l3.22 3.22V3a.75.75 0 0 1 .75-.75Zm-9 13.5a.75.75 0 0 1 .75.75v2.25a1.5 1.5 0 0 0 1.5 1.5h13.5a1.5 1.5 0 0 0 1.5-1.5V16.5a.75.75 0 0 1 1.5 0v2.25a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3V16.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd" />
                </svg>
                {{ __('Import Data') }}
            </x-danger-button>
        </div>
    </div>
    <div class="relative overflow-x-auto sm:rounded-lg">
        <div class="flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between pb-4">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 rtl:inset-r-0 rtl:right-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>
                </div>
                <input type="text" wire:model.live="searchKeyword" id="table-search" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search for items">
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
                    Payment Date
                </th>
                <th scope="col" class="px-6 py-3 text-center">
                    Batch
                </th>
                <th scope="col" class="px-6 py-3 text-center">
                    Name
                </th>
                <th scope="col" class="px-6 py-3 text-center">
                    Phone
                </th>
                <th scope="col" class="px-6 py-3 text-center">
                    Email
                </th>
                <th scope="col" class="px-6 py-3 text-center">
                    Printed
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
                        <a href="{{ route('order.complete', ['order' => $order->id]) }}" class="font-medium hover:underline cursor-pointer">#{{ $order->code }}</a>
                    </th>
                    <td class="px-6 py-4 text-center">
                        {{ $order->payment->paid_at->format('d-m-Y H:i:s') }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        {{ $order->batch->number }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        {{ $order->name }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        {{ $order->phone }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        {{ $order->email }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center items-center">
                            @if($order->printed)
                                <svg class="w-4 h-4 text-emerald-600 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                -
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a type="button" href="{{ route('order.complete', ['order' => $order->id]) }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-xs p-1.5 text-center inline-flex items-center me-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M4.998 7.78C6.729 6.345 9.198 5 12 5c2.802 0 5.27 1.345 7.002 2.78a12.713 12.713 0 0 1 2.096 2.183c.253.344.465.682.618.997.14.286.284.658.284 1.04s-.145.754-.284 1.04a6.6 6.6 0 0 1-.618.997 12.712 12.712 0 0 1-2.096 2.183C17.271 17.655 14.802 19 12 19c-2.802 0-5.27-1.345-7.002-2.78a12.712 12.712 0 0 1-2.096-2.183 6.6 6.6 0 0 1-.618-.997C2.144 12.754 2 12.382 2 12s.145-.754.284-1.04c.153-.315.365-.653.618-.997A12.714 12.714 0 0 1 4.998 7.78ZM12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd"/>
                            </svg>
                            <span class="sr-only">Icon description</span>
                        </a>
                        <a type="button" href="{{ route('shipping.label.generate', ['order' => $order->id]) }}" class="text-white bg-amber-700 hover:bg-amber-800 focus:ring-4 focus:outline-none focus:ring-amber-300 font-medium rounded-lg text-xs p-1.5 text-center inline-flex items-center me-2 dark:bg-amber-600 dark:hover:bg-amber-700 dark:focus:ring-amber-800">
                            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M8 3a2 2 0 0 0-2 2v3h12V5a2 2 0 0 0-2-2H8Zm-3 7a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h1v-4a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v4h1a2 2 0 0 0 2-2v-5a2 2 0 0 0-2-2H5Zm4 11a1 1 0 0 1-1-1v-4h8v4a1 1 0 0 1-1 1H9Z" clip-rule="evenodd"/>
                            </svg>
                            <span class="sr-only">Icon description</span>
                        </a>
                        <button type="button" wire:click="selectOrder('{{ $order->id }}')" class="text-white bg-emerald-700 hover:bg-emerald-800 focus:ring-4 focus:outline-none focus:ring-emerald-300 font-medium rounded-lg text-xs p-1.5 text-center inline-flex items-center me-2 dark:bg-emerald-600 dark:hover:bg-emerald-700 dark:focus:ring-emerald-800 cursor-pointer">
                            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z" clip-rule="evenodd"/>
                            </svg>
                            <span class="sr-only">Icon description</span>
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="pt-4">
            {{ $orders->links() }}
        </div>
    </div>

    <x-modal name="confirm-order-completion" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="completeOrder" class="p-6">

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Are you sure you want to complete this order?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Please input the tracking code for complete this order.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="tracking_code" value="{{ __('Tracking Code') }}" class="sr-only"/>

                <x-text-input
                    wire:model="form.trackingCode"
                    id="tracking_code"
                    name="tracking_code"
                    class="mt-1 block w-full"
                />

                <x-input-error :messages="$errors->get('form.trackingCode')" class="mt-2"/>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ms-3">
                    {{ __('Complete Order') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <x-modal name="confirm-order-import" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="importData" class="p-6">

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Import Data') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Please upload the excel file that has been filled with the tracking code data.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="import_file" :value="__('File')" class="required"/>
                <input wire:model="importOrderForm.importFile"
                       class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                       aria-describedby="file_input_help" id="file_input" type="file"
                       accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">Supported file format is .xlsx.</p>
                <x-input-error class="mt-2" :messages="$errors->get('importOrderForm.importFile')"/>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ms-3">
                    {{ __('Import Now!') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
</div>
