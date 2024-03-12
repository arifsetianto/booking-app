<?php

use App\Models\Order;
use App\ValueObject\OrderStatus;
use Livewire\Volt\Component;
use function Livewire\Volt\{state};

new class extends Component {
    public int $totalIncomingOrders = 0;
    public int $totalVerifiedOrders = 0;
    public int $totalCompletedOrders = 0;

    public function mount(): void
    {
        $this->totalIncomingOrders = Order::where('status', OrderStatus::CONFIRMED)->count();
        $this->totalVerifiedOrders = Order::where('status', OrderStatus::VERIFIED)->count();
        $this->totalCompletedOrders = Order::where('status', OrderStatus::COMPLETED)->count();
    }
};

?>

<div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="max-w-sm w-full bg-white shadow rounded-lg dark:bg-gray-800 p-4 md:p-6">
            <div class="flex justify-center items-center">
                <div class="text-center">
                    <h5 class="leading-none text-3xl font-bold text-gray-900 dark:text-white pb-2">{{ number_format($totalIncomingOrders) }}</h5>
                    <p class="text-base font-normal text-gray-500 dark:text-gray-400">Total Incoming Orders</p>
                </div>
            </div>
        </div>
        <div class="max-w-sm w-full bg-white shadow rounded-lg dark:bg-gray-800 p-4 md:p-6">
            <div class="flex justify-center items-center">
                <div class="text-center">
                    <h5 class="leading-none text-3xl font-bold text-gray-900 dark:text-white pb-2">{{ number_format($totalVerifiedOrders) }}</h5>
                    <p class="text-base font-normal text-gray-500 dark:text-gray-400">Total Verified Orders</p>
                </div>
            </div>
        </div>
        <div class="max-w-sm w-full bg-white shadow rounded-lg dark:bg-gray-800 p-4 md:p-6">
            <div class="flex justify-center items-center">
                <div class="text-center">
                    <h5 class="leading-none text-3xl font-bold text-gray-900 dark:text-white pb-2">{{ number_format($totalCompletedOrders) }}</h5>
                    <p class="text-base font-normal text-gray-500 dark:text-gray-400">Total Completed Orders</p>
                </div>
            </div>
        </div>
    </div>
</div>
