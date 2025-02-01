<?php

use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\ValueObject\UserStatus;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    //public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public $newPasswordVisible = false;
    public $confirmNewPasswordVisible = false;

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
                    //'name'     => ['required', 'string', 'max:255'],
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

            //$user->name = $validated['name'];
            $user->password = Hash::make($validated['password']);

            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
                event(new Registered($user));
            }

            event(new Verified($user));

            $user->save();

            Auth::login($user);

            if ($user->status->is(UserStatus::NEW)) {
                $this->redirectIntended(default: 'complete-profile', navigate: true);
            } else {
                $this->redirect(RouteServiceProvider::HOME, navigate: true);
            }
        } else {
            $validated = $this->validate(
                [
                    //'name'     => ['required', 'string', 'max:255'],
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

            $user->markEmailAsVerified();

            Auth::login($user);

            //$this->redirect('verify-email', navigate: true);
            redirect()->intended('/complete-profile'.'?verified=1');
        }
    }

    public function toggleVisibilityNewPassword(): void
    {
        $this->newPasswordVisible = !$this->newPasswordVisible;
    }

    public function toggleVisibilityConfirmNewPassword(): void
    {
        $this->confirmNewPasswordVisible = !$this->confirmNewPasswordVisible;
    }
}; ?>

<div>
    <div class="mt-2 mb-6 text-xl text-gray-600">
        <h4 class="text-center font-semibold">{{ __('Member Account Registration Form') }}</h4>

        <div class="alert alert-warning flex items-center justify-between bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4 rounded mt-4">
            <div class="flex items-center">
                <span class="text-sm">
                    🚨 No Email Verification Needed – Due to an issue with our email server (Mailgun services down), we have skipped the email verification step to ensure a smooth experience.

No need to worry! You can now fully access your account and continue using ThaiQuran services as usual after register

Thank you for your patience and understanding!
                </span>
            </div>
        </div>
    </div>
    <form wire:submit="register">
        <!-- Name -->
        {{--        <div>--}}
        {{--            <x-input-label for="name" :value="__('Name')"/>--}}
        {{--            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required--}}
        {{--                          autofocus autocomplete="name"/>--}}
        {{--            <x-input-error :messages="$errors->get('name')" class="mt-2"/>--}}
        {{--        </div>--}}

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')"/>
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required
                          autocomplete="username"/>
            <x-input-error :messages="$errors->get('email')" class="mt-2"/>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('New Password')"/>

            <div class="relative">
                <x-text-input wire:model="password" id="password" class="block w-full pr-10"
                              type="{{ $newPasswordVisible ? 'text' : 'password' }}"
                              name="password"
                              required autocomplete="new-password"/>
                <button type="button" wire:click="toggleVisibilityNewPassword"
                        class="absolute inset-y-0 right-0 px-2 flex items-center text-gray-500 hover:text-gray-700 focus:outline-none">
                    @if ($newPasswordVisible)
                        <x-heroicon-s-eye-slash class="h-5 w-5"/>
                    @else
                        <x-heroicon-s-eye class="h-5 w-5"/>
                    @endif
                </button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2"/>
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm New Password')"/>

            <div class="relative">
                <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block w-full pr-10"
                              type="{{ $confirmNewPasswordVisible ? 'text' : 'password' }}"
                              name="password_confirmation" required autocomplete="new-password"/>
                <button type="button" wire:click="toggleVisibilityConfirmNewPassword"
                        class="absolute inset-y-0 right-0 px-2 flex items-center text-gray-500 hover:text-gray-700 focus:outline-none">
                    @if ($confirmNewPasswordVisible)
                        <x-heroicon-s-eye-slash class="h-5 w-5"/>
                    @else
                        <x-heroicon-s-eye class="h-5 w-5"/>
                    @endif
                </button>
            </div>

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
