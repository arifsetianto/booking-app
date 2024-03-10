<?php

use App\Models\Religion;
use App\Models\User;
use App\ValueObject\Gender;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use function Livewire\Volt\{state};

new class extends Component {
    public ?string $name = '';
    public string $email = '';
    public ?string $phone = '';
    public ?string $instagram = '';
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
                'gender'    => ['required', Rule::in(Gender::getValues())],
            ]
        );

        $user->fill(Arr::only($validated, ['name', 'email']));

        $user->profile->phone = $this->phone;
        $user->profile->instagram = $this->instagram;
        $user->profile->gender = Gender::from($this->gender);
        $user->profile->religion()->associate(Religion::find($this->religion));

        $user->save();
        $user->profile->save();

        $this->dispatch('profile-updated', name: $user->name);
    }
};

?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
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
                          autocomplete="username" readonly/>
            <x-input-error class="mt-2" :messages="$errors->get('email')"/>
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

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save Profile') }}</x-primary-button>

            <x-action-message class="me-3" on="profile-updated">
                {{ __('Profile Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>
