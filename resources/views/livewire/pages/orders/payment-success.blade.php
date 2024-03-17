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

    public function redirectToTrackingOrder(): void
    {
        $this->redirectRoute(name: 'orders.tracking.status', parameters: ['order' => $this->order->id]);
    }
};

?>

<div>
    <div class="text-center">
        <h1 class="mb-4 text-xl font-semibold leading-none tracking-tight text-gray-900 md:text-2xl lg:text-3xl dark:text-white">
            #{{ $order->code }}</h1>
        <p class="font-semibold text-lg">Thank you for initiating your order</p>
        <p class="px-32">Your order will be processed as soon as we verify receiver's ID and the payment, usually within 1-7 working days. We will update your confirmation status in your login account. We've sent you status/tracking link, please click the button below:</p>
        <div class="py-6">
            <x-primary-button wire:click="redirectToTrackingOrder">
                {{ __('Check your order status here!') }}
            </x-primary-button>
        </div>
        <p class="mt-5 text-sm text-gray-500">Thank you for your cooperation.</p>
    </div>
</div>
