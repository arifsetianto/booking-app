<div>
    <div class="flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between pb-4">
        <x-primary-button wire:click="redirectToCreateBatchPage">
            {{ __('Create Batch') }}
        </x-primary-button>
    </div>
    <div class="relative overflow-x-auto sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3 text-center">
                    Batch Number
                </th>
                <th scope="col" class="px-6 py-3 text-center">
                    Total Stock
                </th>
                <th scope="col" class="px-6 py-3 text-center">
                    Purchased Stock
                </th>
                <th scope="col" class="px-6 py-3 text-center">
                    Available Stock
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
            @foreach($batches as $batch)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <th scope="row" class="px-6 py-4 font-medium text-center text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $batch->number }}.
                    </th>
                    <td class="px-6 py-4 text-center">
                        {{ $batch->total_stock }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        {{ $batch->purchased_stock }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        {{ $batch->total_stock - $batch->purchased_stock }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="bg-{{ $batch->status->getColor() }}-100 text-{{ $batch->status->getColor() }}-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-{{ $batch->status->getColor() }}-900 dark:text-{{ $batch->status->getColor() }}-300">
                            {{ $batch->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        {{ $batch->created_at->format('d-m-Y H:i:s') }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('batch.edit', ['batch' => $batch->id]) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline cursor-pointer">Edit</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="pt-4">
            {{ $batches->links() }}
        </div>
    </div>
</div>
