<?php

use App\Models\Order;
use App\Models\User;
use App\ValueObject\OrderStatus;
use Livewire\Volt\Component;
use function Livewire\Volt\{state};

new class extends Component {
    public int $totalRegisteredUsers = 0;
    public int $totalIncome = 0;
    public int $totalIncomingOrders = 0;
    public int $totalVerifiedOrders = 0;
    public int $totalCompletedOrders = 0;

    public function mount(): void
    {
        $this->totalRegisteredUsers = User::whereNotNull('profile_id')->count();
        $this->totalIncome = Order::whereIn('status', [OrderStatus::VERIFIED, OrderStatus::COMPLETED])->sum('amount');
        $this->totalIncomingOrders = Order::where('status', OrderStatus::CONFIRMED)->count();
        $this->totalVerifiedOrders = Order::where('status', OrderStatus::VERIFIED)->count();
        $this->totalCompletedOrders = Order::where('status', OrderStatus::COMPLETED)->count();
    }
};

?>

<div>
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="max-w-sm w-full bg-white shadow rounded-lg dark:bg-gray-800 p-4 md:p-6">
            <div class="flex justify-center items-center">
                <div class="text-center">
                    <h5 class="leading-none text-3xl font-bold text-gray-900 dark:text-white pb-2">{{ number_format($totalRegisteredUsers) }}</h5>
                    <p class="text-base font-normal text-gray-500 dark:text-gray-400">Total Registered Users</p>
                </div>
            </div>
        </div>
        <div class="max-w-sm w-full bg-white shadow rounded-lg dark:bg-gray-800 p-4 md:p-6">
            <div class="flex justify-center items-center">
                <div class="text-center">
                    <h5 class="leading-none text-3xl font-bold text-gray-900 dark:text-white pb-2">฿ {{ number_format($totalIncome) }}</h5>
                    <p class="text-base font-normal text-gray-500 dark:text-gray-400">Total Income</p>
                </div>
            </div>
        </div>
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
