<?php

use App\Models\Profile;
use App\Models\Source;
use App\Models\User;
use App\ValueObject\UserStatus;
use Illuminate\Auth\Events\Verified;
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
        $this->phone = $user->profile?->phone;
        $this->instagram = $user->profile?->instagram;
        $this->source = $user->profile?->source?->id;

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
                    'email',
                    'max:255',
                    Rule::unique(User::class)->ignore($user->id)
                ],
                'phone'     => ['required', 'numeric', 'digits_between:8,30'],
                'instagram' => ['nullable', 'string', 'max:100'],
                'source'    => ['required', 'uuid', 'exists:sources,id'],
            ]
        );

        $user->fill(Arr::only($validated, ['name', 'email']));
        $user->status = UserStatus::COMPLETED;

        if (!$user->profile) {
            $user->profile()->associate(Profile::create());
        }

        $user->profile->phone = $this->phone;
        $user->profile->instagram = $this->instagram ?? null;
        $user->profile->source()->associate(Source::findOrFail($this->source));

        $user->save();
        $user->profile->save();

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        if ($user->hasVerifiedEmail()) {
            event(new Verified($user));
        }

        //$this->dispatch('profile-updated', name: $user->name);

        $this->redirectIntended(default: '/home', navigate: true);
    }
};

?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("To continue the booking process, please complete your profile data.") }}
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
            <div class="flex mt-1 w-full">
                  <span
                      class="inline-flex items-center px-3 text-sm text-gray-900 border rounded-e-0 border-gray-300 rounded-s-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                    @
                  </span>
                <x-text-input wire:model="instagram" id="instagram" name="instagram" type="text"
                              class="block w-full rounded-none rounded-e-lg border text-gray-900 flex-1 min-w-0 text-sm border-gray-300 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                              autofocus autocomplete="instagram"/>
            </div>
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
            <x-primary-button>{{ __('Save And Continue Booking') }}</x-primary-button>

            <x-action-message class="me-3" on="profile-updated">
                {{ __('Profile Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>
