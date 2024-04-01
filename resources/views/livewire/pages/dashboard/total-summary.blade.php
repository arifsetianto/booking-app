<?php

use App\Models\Order;
use App\Models\User;
use App\ValueObject\OrderStatus;
use Livewire\Volt\Component;
use function Livewire\Volt\{state};

new class extends Component {
    public int $totalRegisteredUsers = 0;
    public int $totalUsersOrdered = 0;
    public int $totalOrders = 0;
    public int $totalIncome = 0;
    public int $totalPendingOrders = 0;
    public int $totalIncomingOrders = 0;
    public int $totalVerifiedOrders = 0;
    public int $totalCompletedOrders = 0;
    public int $totalRevisedOrders = 0;

    public function mount(): void
    {
        $this->totalRegisteredUsers = User::whereNotNull('profile_id')->count();
        $this->totalUsersOrdered =
            User::whereNotNull('profile_id')->whereIn(
                'id',
                Order::select('user_id')->whereIn(
                    'status',
                    [
                        OrderStatus::PENDING,
                        OrderStatus::CONFIRMED,
                        OrderStatus::VERIFIED,
                        OrderStatus::COMPLETED,
                        OrderStatus::REVISED
                    ]
                )->get()
            )->count();
        $this->totalOrders = Order::whereIn('status', [OrderStatus::CONFIRMED, OrderStatus::VERIFIED, OrderStatus::COMPLETED])->count();
        $this->totalIncome = Order::whereIn('status', [OrderStatus::VERIFIED, OrderStatus::COMPLETED])->sum('amount');
        $this->totalPendingOrders = Order::where('status', OrderStatus::PENDING)->count();
        $this->totalIncomingOrders = Order::where('status', OrderStatus::CONFIRMED)->count();
        $this->totalVerifiedOrders = Order::where('status', OrderStatus::VERIFIED)->count();
        $this->totalCompletedOrders = Order::where('status', OrderStatus::COMPLETED)->count();
        $this->totalRevisedOrders = Order::where('status', OrderStatus::REVISED)->count();
    }
};

?>

<div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pb-3">
        <div class="max-w-xl w-full bg-white shadow rounded-lg dark:bg-gray-800 p-4 md:p-6">
            <div class="flex justify-center items-center">
                <div class="text-center">
                    <h5 class="leading-none text-3xl font-bold text-gray-900 dark:text-white pb-2">{{ number_format($totalRegisteredUsers) }}</h5>
                    <p class="text-base font-normal text-gray-500 dark:text-gray-400">Total Registered Users</p>
                </div>
            </div>
        </div>
        <div class="max-w-xl w-full bg-white shadow rounded-lg dark:bg-gray-800 p-4 md:p-6">
            <div class="flex justify-center items-center">
                <div class="text-center">
                    <h5 class="leading-none text-3xl font-bold text-gray-900 dark:text-white pb-2">{{ number_format($totalOrders) }}</h5>
                    <p class="text-base font-normal text-gray-500 dark:text-gray-400">Total Orders</p>
                </div>
            </div>
        </div>
        <div class="max-w-xl w-full bg-white shadow rounded-lg dark:bg-gray-800 p-4 md:p-6">
            <div class="flex justify-center items-center">
                <div class="text-center">
                    <h5 class="leading-none text-3xl font-bold text-gray-900 dark:text-white pb-2">
                        ฿ {{ number_format($totalIncome) }}</h5>
                    <p class="text-base font-normal text-gray-500 dark:text-gray-400">Total Income</p>
                </div>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 pt-3">
        <div class="max-w-sm w-full bg-white shadow rounded-lg dark:bg-gray-800 p-4 md:p-6">
            <div class="flex justify-center items-center">
                <div class="text-center">
                    <h5 class="leading-none text-3xl font-bold text-gray-900 dark:text-white pb-2">{{ number_format($totalPendingOrders) }}</h5>
                    <p class="text-base font-normal text-gray-500 dark:text-gray-400">Total Pending Orders</p>
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
                    <p class="text-base font-normal text-gray-500 dark:text-gray-400">Total Shipped Orders</p>
                </div>
            </div>
        </div>
        <div class="max-w-sm w-full bg-white shadow rounded-lg dark:bg-gray-800 p-4 md:p-6">
            <div class="flex justify-center items-center">
                <div class="text-center">
                    <h5 class="leading-none text-3xl font-bold text-gray-900 dark:text-white pb-2">{{ number_format($totalRevisedOrders) }}</h5>
                    <p class="text-base font-normal text-gray-500 dark:text-gray-400">Total Revised Orders</p>
                </div>
            </div>
        </div>
    </div>
</div>
