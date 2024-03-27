<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Order') }}
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
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="col-span-3">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <livewire:pages.orders.archive-order-detail />
                        </div>
                    </div>
                </div>
                <div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <livewire:pages.orders.other-orders-list />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-app-layout>
