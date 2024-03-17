<?php

use App\Models\Order;
use Illuminate\Http\Request;
use Livewire\Volt\Component;
use function Livewire\Volt\{state};

new class extends Component {
    public Order $order;

    public function mount(Request $request): void
    {
        $this->order = Order::findOrFail($request->route('order'));
    }
};

?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Booking Preview') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("The following is the booking data that you fill in.") }}
        </p>
    </header>

    <div class="mt-6 space-y-6">
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <p class="text-sm text-gray-500">Receiver Name</p>
                <p class="pt-1 text-sm">{{ $order->orderItem->receiver_th_name }} ({{ $order->orderItem->receiver_en_name }})</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Order Date</p>
                <p class="pt-1 text-sm">{{ $order->orderItem->created_at->format('d-m-Y H:i:s') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Order For</p>
                <p class="pt-1 text-sm">{{ $order->orderItem->designation->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Gender</p>
                <p class="pt-1 text-sm capitalize">{{ $order->orderItem->gender }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Religion</p>
                <p class="pt-1 text-sm">{{ $order->orderItem->religion->name }}</p>
            </div>
        </div>
    </div>
</section>
