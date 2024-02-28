<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use function Livewire\Volt\{state};

new #[Layout('layouts.guest')] class extends Component
{
    public function agree(): void
    {
        $this->redirectIntended(default: '/login-email', navigate: true);
    }
};

?>

<div>
    <div class="text-center">

        <h1 class="mb-4 text-2xl font-extrabold leading-none tracking-tight text-gray-900 md:text-3xl lg:text-4xl dark:text-white">Order your FREE <span class="text-blue-600 dark:text-blue-500">ThaiQuran</span> today!</h1>
        <h1 class="text-2xl font-extrabold dark:text-white mt-5">Available Stock 500</h1>

    </div>
    <div class="my-5">

        <h2 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">Rules:</h2>
        <ul class="max-w-md space-y-1 text-gray-500 list-disc list-inside dark:text-gray-400">
            <li>We DO NOT ship outside Thailand</li>
            <li>This service strictly for Thai Nationality</li>
            <li>This service strictly for 1 ThaiQuran per 1 Thai ID, please note our policy to prioritize to serving
                those who have not yet received any ThaiQuran
            </li>
            <li>This step is required to collect your information so we can verify your Order History</li>
            <li>We understand if you are worried about missing the chance to get your FREE ThaiQuran, InsyaAllah there
                will be the next Order Batch for you to join! or you can always access ThaiQuran Online version for
                FREE!
            </li>
        </ul>

    </div>
    <div class="p-4 md:p-5">
        <button wire:click="agree" class="text-white inline-flex w-full justify-center bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            Yes, I Agree
        </button>
    </div>
</div>
