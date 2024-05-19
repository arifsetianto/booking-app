<?php

use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\ValueObject\UserStatus;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        if (null !==
            $user =
                User::query()->where(DB::raw('lower(email)'), strtolower($this->email))->whereNull('password')->first(
                )) {
            $validated = $this->validate(
                [
                    'name'     => ['required', 'string', 'max:255'],
                    'email'    => [
                        'required',
                        'string',
                        'lowercase',
                        'email',
                        'max:255',
                        //'unique:'.User::class
                    ],
                    'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
                ]
            );

            $user->name = $validated['name'];
            $user->password = Hash::make($validated['password']);

            $user->save();

            Auth::login($user);

            $this->redirect(RouteServiceProvider::HOME, navigate: true);
        } else {
            $validated = $this->validate(
                [
                    'name'     => ['required', 'string', 'max:255'],
                    'email'    => [
                        'required',
                        'string',
                        'lowercase',
                        'email',
                        'max:255',
                        'unique:' . User::class
                    ],
                    'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
                ]
            );

            $validated['password'] = Hash::make($validated['password']);

            event(new Registered($user = User::create([...$validated, 'status' => UserStatus::NEW])));

            $user->roles()->attach(Role::where('name', 'customer')->first());
            $user->profile()->associate(Profile::create());

            $user->save();

            Auth::login($user);

            $this->redirect('verify-email', navigate: true);
        }
    }
}; ?>

<div>
    <form wire:submit="register">
        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')"/>
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required
                          autofocus autocomplete="name"/>
            <x-input-error :messages="$errors->get('name')" class="mt-2"/>
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')"/>
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required
                          autocomplete="username"/>
            <x-input-error :messages="$errors->get('email')" class="mt-2"/>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')"/>

            <x-text-input wire:model="password" id="password" class="block mt-1 w-full"
                          type="password"
                          name="password"
                          required autocomplete="new-password"/>

            <x-input-error :messages="$errors->get('password')" class="mt-2"/>
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')"/>

            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full"
                          type="password"
                          name="password_confirmation" required autocomplete="new-password"/>

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2"/>
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
               href="{{ route('guest.login') }}" wire:navigate>
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</div>
