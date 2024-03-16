<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="px-6 py-3 text-gray-900">
                <livewire:pages.dashboard.total-summary />
            </div>
            <div class="px-6 py-3 text-gray-900">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <livewire:components.dashboard.order-source-chart />
                    <livewire:components.dashboard.order-religion-chart />
                    <livewire:components.dashboard.order-gender-chart />
                </div>
            </div>
            <div class="px-6 py-3 text-gray-900">
                <livewire:components.dashboard.order-city-chart />
            </div>
            <div class="px-6 py-3 text-gray-900">
                <livewire:pages.dashboard.latest-transaction />
            </div>
        </div>
    </div>
</x-admin-app-layout>
