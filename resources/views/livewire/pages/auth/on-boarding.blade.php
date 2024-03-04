<?php

use App\Models\Batch;
use App\ValueObject\BatchStatus;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use function Livewire\Volt\{state};

new #[Layout('layouts.guest')] class extends Component {
    public ?Batch $batch = null;

    public function mount(): void
    {
        $this->batch = Batch::where('status', BatchStatus::PUBLISHED)->first();
    }

    public function agree(): void
    {
        $this->redirect(url: '/login-email', navigate: true);
    }
};

?>

<div>

    <div class="sm:p-2 md:p-4">
        <h5 class="mb-4 text-2xl font-extrabold text-center leading-none tracking-tight text-gray-900 md:text-3xl lg:text-4xl dark:text-gray-400">
            Order your FREE<br/><span class="text-blue-600 dark:text-blue-500">ThaiQuran</span> today!</h5>
        <div class="flex flex-col justify-center py-5 items-center text-gray-900 dark:text-white">
            @if($batch)
                <span class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-400">Available Stock</span>
                <span class="text-5xl font-extrabold tracking-tight text-gray-900">{{ $batch->total_stock - $batch->purchased_stock }}</span>
            @else
                <span class="text-xl font-semibold mb-2 text-gray-700 dark:text-gray-400">Stock Unavailable</span>
            @endif
        </div>
        <ul role="list" class="space-y-5 my-7">
            <li class="flex items-start">
                <svg class="flex-shrink-0 w-4 h-4 text-blue-700 dark:text-blue-500" aria-hidden="true"
                     xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                </svg>
                <span class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400 ms-3">We DO NOT ship outside Thailand</span>
            </li>
            <li class="flex items-start">
                <svg class="flex-shrink-0 w-4 h-4 text-blue-700 dark:text-blue-500" aria-hidden="true"
                     xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                </svg>
                <span class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400 ms-3">This service strictly for Thai Nationality</span>
            </li>
            <li class="flex items-start">
                <svg class="flex-shrink-0 w-4 h-4 text-blue-700 dark:text-blue-500" aria-hidden="true"
                     xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                </svg>
                <span class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400 ms-3">This service strictly for 1 ThaiQuran per 1 Thai ID, please note our policy to prioritize to serving those who have not yet received any ThaiQuran</span>
            </li>
            <li class="flex items-start">
                <svg class="flex-shrink-0 w-4 h-4 text-blue-700 dark:text-blue-500" aria-hidden="true"
                     xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                </svg>
                <span class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400 ms-3">This step is required to collect your information so we can verify your Order History</span>
            </li>
            <li class="flex items-start">
                <svg class="flex-shrink-0 w-4 h-4 text-blue-700 dark:text-blue-500" aria-hidden="true"
                     xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                </svg>
                <span class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400 ms-3">We understand if you are worried about missing the chance to get your FREE ThaiQuran, InsyaAllah there will be the next Order Batch for you to join! or you can always access ThaiQuran Online version for FREE!</span>
            </li>
        </ul>
        <button type="button" wire:click="agree"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-200 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-900 font-medium rounded-lg text-sm px-5 py-2.5 inline-flex justify-center w-full text-center">
            Yes, I Agree
        </button>
    </div>
</div>
