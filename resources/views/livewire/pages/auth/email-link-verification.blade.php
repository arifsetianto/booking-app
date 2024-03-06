<?php

use App\Event\Auth\UserLoginRequested;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use function Livewire\Volt\{state};

new #[Layout('layouts.guest')] class extends Component {
    #[Url]
    public string $email = '';

    public function resendLink(): void
    {
        event(new UserLoginRequested($this->email));

        Session::flash('message', 'The login link has been sent, please check your inbox or spam folder in your email.');
    }
};

?>

<div>
    <div class="sm:p-2 md:p-4">
        @if (session()->has('message'))
            <x-alert-success :message="session('message')"/>
        @endif
        @if (session()->has('error'))
            <x-alert-error :message="session('error')"/>
        @endif
        <div class="space-y-5 mb-7">
            <h5 class="mb-5 text-2xl font-medium text-center text-gray-500 dark:text-gray-400">Check your inbox
                email!</h5>
            <p class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400">Click on the link or button
                that we sent to
                <b>{{ $this->email }}</b> to verify your email account.</p>
        </div>
        <div>
            <p class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400 mb-5">Not receiving emails in
                your inbox or spam folder? Let's resend it!</p>
            <x-primary-button wire:click="resendLink" class="px-5 py-2.5 inline-flex justify-center w-full text-center">
                {{ __('Resend Link') }}
            </x-primary-button>
        </div>
    </div>
</div>
