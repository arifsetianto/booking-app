<?php

use App\Models\Religion;
use App\Models\Source;
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
    public ?string $source = '';

    public array $sources = [];

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
        $this->source = $user->profile->source?->id;

        $this->sources = Source::get()->map(fn($item) => ['value' => $item->id, 'label' => $item->name])->toArray();
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
                'instagram' => ['nullable', 'string', 'max:100'],
                'source'    => ['required', 'uuid', 'exists:sources,id'],
            ]
        );

        $user->fill(Arr::only($validated, ['name', 'email']));

        $user->profile->phone = $this->phone;
        $user->profile->instagram = $this->instagram;
        $user->profile->source()->associate(Source::findOrFail($this->source));

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
            <x-input-label for="name" :value="__('Name')" class="required"/>
            <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full"
                          autofocus autocomplete="name"/>
            <x-input-error class="mt-2" :messages="$errors->get('name')"/>
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" class="required"/>
            <x-text-input wire:model="email" id="email" name="email" type="email" class="mt-1 block w-full"
                          autocomplete="username" readonly/>
            <x-input-error class="mt-2" :messages="$errors->get('email')"/>
        </div>

        <div>
            <x-input-label for="phone" :value="__('Mobile No.')" class="required"/>
            <x-text-input wire:model="phone" id="phone" name="phone" type="text" class="mt-1 block w-full"
                          autofocus autocomplete="phone"/>
            <x-input-error class="mt-2" :messages="$errors->get('phone')"/>
        </div>

        <div>
            <x-input-label for="instagram" :value="__('Instagram (Optional)')"/>
            <x-text-input wire:model="instagram" id="instagram" name="instagram" type="text" class="mt-1 block w-full"
                          autofocus autocomplete="instagram"/>
            <x-input-error class="mt-2" :messages="$errors->get('instagram')"/>
        </div>

        <div>
            <x-input-label for="source" :value="__('How do you know us')" class="required"/>
            <x-select-input wire:model="source" id="source" name="source" class="mt-1 block w-full"
                            :options="$sources"
                            autofocus/>
            <x-input-error class="mt-2" :messages="$errors->get('source')"/>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save Profile') }}</x-primary-button>

            <x-action-message class="me-3" on="profile-updated">
                {{ __('Profile Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>
