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
        <p class="font-semibold text-lg">Thank You!</p>
        <p class="px-32">Your order has been submitted. We will check the validity of your information.<br/><span
                class="font-semibold">ThaiQuran Team will need 7 Working Days to process the order.</span><br/>Please be
            patient and check your status via email link.</p>
        <div class="py-6">
            <x-primary-button wire:click="redirectToTrackingOrder">
                {{ __('Check your order status here!') }}
            </x-primary-button>
        </div>
    </div>
</div>
