<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use function Livewire\Volt\{state};

new #[Layout('layouts.guest')] class extends Component {
    #[Url]
    public string $email = '';
};

?>

<div>

    <div class="sm:p-2 md:p-4">
        <div class="space-y-5 mb-7">
            <h5 class="mb-5 text-2xl font-medium text-center text-gray-500 dark:text-gray-400">Check your inbox email!</h5>
            <p class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400">Click on the link or button that we sent to
                <b>{{ $this->email }}</b> to verify your email account.</p>
        </div>
        <div>
            <p class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400 mb-5">Not receiving emails in your inbox or spam folder? Let's resend it!</p>
            <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-200 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-900 font-medium rounded-lg text-sm px-5 py-2.5 inline-flex justify-center w-full text-center">Resend Link</button>
        </div>
    </div>

</div>
