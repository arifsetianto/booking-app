<?php

use App\Models\Religion;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\ValueObject\Gender;
use App\ValueObject\UserStatus;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use function Livewire\Volt\{state};

new class extends Component {
    public ?string $name = '';
    public string $email = '';
    public ?string $phone = '';
    public ?string $instagram = '';
    public ?string $address = '';
    public ?string $gender = '';
    public ?string $religion = '';

    public array $genders = [];
    public array $religions = [];

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->profile->phone;
        $this->instagram = $user->profile->instagram;
        $this->address = $user->profile->address;
        $this->gender = $user->profile->gender?->value;
        $this->religion = $user->profile->religion?->id;

        $this->genders = Gender::getOptions();
        $this->religions = Religion::get()->map(fn($item) => ['value' => $item->id, 'label' => $item->name])->toArray();
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $this->validate(
            [
                'name'      => ['required', 'string', 'max:255'],
                'email'     => [
                    'required',
                    'string',
                    'lowercase',
                    'email',
                    'max:255',
                    Rule::unique(User::class)->ignore($user->id)
                ],
                'phone'     => ['required', 'string', 'max:30'],
                'instagram' => ['required', 'string', 'max:100'],
                'address'   => ['required', 'string'],
                'gender'    => ['required', Rule::in(Gender::getValues())],
                'religion'  => ['required', 'uuid', 'exists:religions,id']
            ]
        );

        $user->fill(Arr::only($validated, ['name', 'email']));
        $user->status = UserStatus::COMPLETED;

        $user->profile->phone = $this->phone;
        $user->profile->instagram = $this->instagram;
        $user->profile->address = $this->address;
        $user->profile->gender = Gender::from($this->gender);
        $user->profile->religion()->associate(Religion::find($this->religion));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();
        $user->profile->save();

        //$this->dispatch('profile-updated', name: $user->name);

        $this->redirectIntended(default: '/orders', navigate: true);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: RouteServiceProvider::HOME);

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
};

?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("To continue the booking process, please complete your profile data first.") }}
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-6">
        <div>
            <x-input-label for="name" :value="__('Name')"/>
            <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full"
                          autofocus autocomplete="name"/>
            <x-input-error class="mt-2" :messages="$errors->get('name')"/>
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')"/>
            <x-text-input wire:model="email" id="email" name="email" type="email" class="mt-1 block w-full"
                          autocomplete="username"/>
            <x-input-error class="mt-2" :messages="$errors->get('email')"/>

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button wire:click.prevent="sendVerification"
                                class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="phone" :value="__('Phone')"/>
            <x-text-input wire:model="phone" id="phone" name="phone" type="text" class="mt-1 block w-full"
                          autofocus autocomplete="phone"/>
            <x-input-error class="mt-2" :messages="$errors->get('phone')"/>
        </div>

        <div>
            <x-input-label for="instagram" :value="__('Instagram')"/>
            <x-text-input wire:model="instagram" id="instagram" name="instagram" type="text" class="mt-1 block w-full"
                          autofocus autocomplete="instagram"/>
            <x-input-error class="mt-2" :messages="$errors->get('instagram')"/>
        </div>

        <div>
            <x-input-label for="gender" :value="__('Gender')"/>
            <x-select-input wire:model="gender" id="gender" name="gender" class="mt-1 block w-full"
                            :options="$genders"
                            autofocus/>
            <x-input-error class="mt-2" :messages="$errors->get('gender')"/>
        </div>

        <div>
            <x-input-label for="religion" :value="__('Religion')"/>
            <x-select-input wire:model="religion" id="religion" name="religion" class="mt-1 block w-full"
                            :options="$religions"
                            autofocus/>
            <x-input-error class="mt-2" :messages="$errors->get('religion')"/>
        </div>

        <div>
            <x-input-label for="address" :value="__('Address')"/>
            <x-text-area wire:model="address" id="address" name="address" class="mt-1 block w-full"
                         autofocus autocomplete="address"/>
            <x-input-error class="mt-2" :messages="$errors->get('address')"/>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save And Continue Booking') }}</x-primary-button>

            <x-action-message class="me-3" on="profile-updated">
                {{ __('Profile Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>
