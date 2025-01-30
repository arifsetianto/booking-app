<div>
    <div class="relative overflow-x-auto sm:rounded-lg">
        <div class="flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between pb-4">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 rtl:inset-r-0 rtl:right-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>
                </div>
                <input type="text" wire:model.live="searchKeyword" id="table-search" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search for customers">
            </div>
        </div>
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3 text-center">
                    Name
                </th>
                <th scope="col" class="px-6 py-3 text-center">
                    Email
                </th>
                <th scope="col" class="px-6 py-3 text-center">
                    Phone
                </th>
                <th scope="col" class="px-6 py-3 text-center">
                    Instagram
                </th>
                <th scope="col" class="px-6 py-3 text-center">
                    Status
                </th>
                <th scope="col" class="px-6 py-3 text-center">
                    Created At
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($customers as $customer)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <th scope="row" class="px-6 py-4 font-medium text-center text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $customer->name }}
                    </th>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center">
                            @if($customer->email_verified_at)
                                <x-heroicon-s-check-circle class="w-4 h-4 text-green-500" />
                            @endif
                            <span class="ml-2">{{ $customer->email }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        {{ $customer->profile?->phone ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        {{ $customer->profile?->instagram ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="bg-{{ $customer->status->getColor() }}-100 text-{{ $customer->status->getColor() }}-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-{{ $customer->status->getColor() }}-900 dark:text-{{ $customer->status->getColor() }}-300">
                            {{ $customer->status->getLabel() }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        {{ $customer->created_at->format('d-m-Y H:i:s') }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="pt-4">
            {{ $customers->links() }}
        </div>
    </div>
</div>
