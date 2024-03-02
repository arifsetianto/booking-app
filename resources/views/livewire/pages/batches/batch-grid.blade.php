<div>
    <div class="relative overflow-x-auto sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">
                    Batch Number
                </th>
                <th scope="col" class="px-6 py-3">
                    Total Stock
                </th>
                <th scope="col" class="px-6 py-3">
                    Purchased Stock
                </th>
                <th scope="col" class="px-6 py-3">
                    Available Stock
                </th>
                <th scope="col" class="px-6 py-3">
                    Status
                </th>
                <th scope="col" class="px-6 py-3">
                    Created At
                </th>
                <th scope="col" class="px-6 py-3">
                    Action
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($batches as $batch)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $batch->number }}.
                    </th>
                    <td class="px-6 py-4">
                        {{ $batch->total_stock }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $batch->purchased_stock }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $batch->total_stock - $batch->purchased_stock }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="bg-{{ $batch->status->getColor() }}-50 text-{{ $batch->status->getColor() }}-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-{{ $batch->status->getColor() }}-900 dark:text-{{ $batch->status->getColor() }}-300">
                            {{ $batch->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        {{ $batch->created_at->format('d-m-Y H:i:s') }}
                    </td>
                    <td class="px-6 py-4">
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
