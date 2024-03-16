<div>

    <div class="max-w-sm w-full bg-white rounded-lg shadow dark:bg-gray-800 p-4 md:p-6">

        <div class="flex justify-between items-start w-full">
            <div class="flex-col items-center">
                <div class="flex items-center mb-1">
                    <div>
                        <h5 class="text-lg font-semibold leading-none text-gray-900 dark:text-white me-1">Orders by Source</h5>
                        <p class="mt-2 text-sm font-normal text-gray-500 dark:text-gray-400">Here is a graph of the percentage of orders per source.</p>
                    </div>
                </div>
            </div>
        </div>

        <div style="height: 22rem;" class="pt-6">
            <livewire:livewire-pie-chart
                key="{{ $pieChartModel->reactiveKey() }}"
                :pie-chart-model="$pieChartModel"
            />
        </div>
    </div>
</div>
