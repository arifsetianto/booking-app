<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Delivery Order') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session()->has('message'))
                <x-alert-success :message="session('message')"/>
            @endif
            @if (session()->has('error'))
                <x-alert-error :message="session('error')"/>
            @endif
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <livewire:pages.orders.delivery-order-form />
                        </div>
                    </div>
                </div>
                <div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <livewire:pages.orders.booking-order-preview />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
