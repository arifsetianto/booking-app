<?php

use App\Event\Auth\UserLoginRequested;
use App\Livewire\Forms\Auth\LoginEmailForm;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use function Livewire\Volt\{state};

new #[Layout('layouts.guest')] class extends Component {
    public LoginEmailForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        event(new UserLoginRequested($this->form->email));

        $this->redirect(url: '/email-link-verification?email=' . $this->form->email, navigate: true);
    }
};

?>

<div>

    <div class="sm:p-2 md:p-4">
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')"/>

        <form class="space-y-6" wire:submit="login">
            <h5 class="text-xl font-semibold text-center text-gray-900 dark:text-white">Login to {{ config('app.name') }}</h5>
            <div>
                <x-input-label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                               :value="__('Email')"/>
                <x-text-input wire:model="form.email" id="email"
                              class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                              placeholder="Please enter your email address"
                              type="text" name="email" autofocus autocomplete="username"/>
                <x-input-error :messages="$errors->get('form.email')" class="mt-2"/>
            </div>
            <x-primary-button type="submit"
                              class="w-full inline-flex justify-center text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                {{ __('Continue with Email') }}
            </x-primary-button>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-300">
                Protecting your privacy is our priority, by signing up you consent to our <a href="#"
                                                                                             class="text-blue-700 hover:underline dark:text-blue-500">Privacy
                    Policy</a>
            </div>
        </form>
    </div>
</div>
