<?php

use App\Livewire\Forms\LoginForm;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\ValueObject\UserStatus;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public LoginForm $form;
    public $passwordVisible = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        /** @var User $user */
        $user = auth()->user();

        if ($user->status->is(UserStatus::NEW)) {
            $this->redirectIntended(default: 'complete-profile', navigate: true);
        } else {
            $this->redirectIntended(default: RouteServiceProvider::HOME, navigate: true);
        }
    }

    public function toggleVisibility(): void
    {
        $this->passwordVisible = !$this->passwordVisible;
    }
}; ?>

<div>
    <div class="mb-2 text-xl text-gray-600 font-semibold text-center">
        {{ __('Hi, welcome back') }}
    </div>

    <div class="mb-4 text-sm text-gray-600 text-center">
        {{ __('Don\'t have an account?') }}
        <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
           href="{{ route('register') }}" wire:navigate>Register Now</a>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')"/>

    <form wire:submit="login">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')"/>
            <x-text-input wire:model="form.email" id="email" class="block mt-1 w-full" type="email" name="email"
                          required autofocus autocomplete="username"/>
            <x-input-error :messages="$errors->get('email')" class="mt-2"/>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')"/>

            <div class="relative">
                <x-text-input wire:model="form.password" id="password" class="block w-full pr-10"
                              type="{{ $passwordVisible ? 'text' : 'password' }}"
                              name="password"
                              required autocomplete="current-password"/>
                <button type="button" wire:click="toggleVisibility"
                        class="absolute inset-y-0 right-0 px-2 flex items-center text-gray-500 hover:text-gray-700 focus:outline-none">
                    @if ($passwordVisible)
                        <x-heroicon-s-eye-slash class="h-5 w-5"/>
                    @else
                        <x-heroicon-s-eye class="h-5 w-5"/>
                    @endif
                </button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2"/>
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox"
                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                   href="{{ route('password.request') }}" wire:navigate>
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</div>

