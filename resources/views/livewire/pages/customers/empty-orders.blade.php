<?php

use App\Models\User;
use Illuminate\Http\Request;
use Livewire\Volt\Component;

new class extends Component {
    public User|null $user = null;

    /**
     * Log the current user out of the application.
     */
    public function mount(Request $request): void
    {
        $this->user = User::find($request->route('customer'));
    }
}; ?>

<div>
    <div class="flex justify-center my-6">
        <img src="{{ asset('images/out-of-stock.png') }}" class="w-32 h-32"/>
    </div>
    <p class="text-center font-semibold text-red-700 my-6">
        Customer with the name "{{ $user->name }}" does not yet have an order.
    </p>
</div>
