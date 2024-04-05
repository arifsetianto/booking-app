<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Stock Unavailable') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="text-center font-semibold text-red-700">
                        Oops, we're out of stock. InsyaAllah you can order in the next batch.<br/>In meanwhile, please access our FREE Online <a href="https://thaiquran.com" target="_blank" class="underline">ThaiQuran</a>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
