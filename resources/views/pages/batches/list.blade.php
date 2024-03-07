<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Batch') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session()->has('message'))
                        <x-alert-success :message="session('message')"/>
                    @endif
                    @if (session()->has('error'))
                        <x-alert-error :message="session('error')"/>
                    @endif

                    <div class="flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between pb-4">
                        <x-primary-button wire:click="redirectToCreateBatchPage">
                            {{ __('Create Batch') }}
                        </x-primary-button>
                    </div>
                    <livewire:components.batch.batch-grid />
                </div>
            </div>
        </div>
    </div>
</x-admin-app-layout>
