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

<div>
    <div class="text-center">
        <h1 class="mb-4 text-xl font-semibold leading-tight tracking-tight text-gray-900 md:text-2xl lg:text-3xl dark:text-white">
            Order #{{ $order->code }}<br/>has been canceled!</h1>
        <p class="mt-5 text-base text-gray-500">Please reorder <a href="{{ route('home') }}" class="underline">here</a>
            so we can process your order again, thank you.</p>
    </div>
</div>
