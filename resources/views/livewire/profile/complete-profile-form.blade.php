<?php

use App\Models\City;
use App\Models\District;
use App\Models\Region;
use App\Models\Religion;
use App\Models\SubDistrict;
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
    public ?string $region = '';
    public ?string $city = '';
    public ?string $district = '';
    public ?string $subDistrict = '';

    public array $genders = [];
    public array $religions = [];
    public array $regions = [];
    public array $cities = [];
    public array $districts = [];
    public array $subDistricts = [];

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
        $this->region = $user->profile->subDistrict?->district?->city?->region?->id;
        $this->city = $user->profile->subDistrict?->district?->city?->id;
        $this->district = $user->profile->subDistrict?->district?->id;
        $this->subDistrict = $user->profile->subDistrict?->id;

        $this->genders = Gender::getOptions();
        $this->religions = Religion::get()->map(fn($item) => ['value' => $item->id, 'label' => $item->name])->toArray();
        $this->regions = Region::get()->map(fn($item) => ['value' => $item->id, 'label' => $item->name])->toArray();

        if ($user->profile->subDistrict?->district?->city) {
            $this->getCitiesByRegion();
        }

        if ($user->profile->subDistrict?->district) {
            $this->getDistrictsByCity();
        }

        if ($user->profile->subDistrict) {
            $this->getSubDistrictsByDistrict();
        }
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
                'name'        => ['required', 'string', 'max:255'],
                'email'       => [
                    'required',
                    'string',
                    'lowercase',
                    'email',
                    'max:255',
                    Rule::unique(User::class)->ignore($user->id)
                ],
                'phone'       => ['required', 'string', 'max:30'],
                'instagram'   => ['required', 'string', 'max:100'],
                'address'     => ['required', 'string'],
                'gender'      => ['required', Rule::in(Gender::getValues())],
                'religion'    => ['required', 'uuid', 'exists:religions,id'],
                'subDistrict' => ['required', 'uuid', 'exists:sub_districts,id']
            ]
        );

        $user->fill(Arr::only($validated, ['name', 'email']));
        $user->status = UserStatus::COMPLETED;

        $user->profile->phone = $this->phone;
        $user->profile->instagram = $this->instagram;
        $user->profile->address = $this->address;
        $user->profile->gender = Gender::from($this->gender);
        $user->profile->religion()->associate(Religion::find($this->religion));
        $user->profile->subDistrict()->associate(SubDistrict::find($this->subDistrict));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();
        $user->profile->save();

        //$this->dispatch('profile-updated', name: $user->name);

        $this->redirectIntended(default: '/home', navigate: true);
    }

    public function getCitiesByRegion(): void
    {
        $this->cities = City::where('region_id', $this->region)->get()->map(
            fn($item) => ['value' => $item->id, 'label' => sprintf('%s (%s)', $item->th_name, $item->en_name)]
        )->toArray();
    }

    public function getDistrictsByCity(): void
    {
        $this->districts =
            District::where('city_id', $this->city)->get()->map(
                fn($item) => ['value' => $item->id, 'label' => sprintf('%s (%s)', $item->th_name, $item->en_name)]
            )->toArray();
    }

    public function getSubDistrictsByDistrict(): void
    {
        $this->subDistricts = SubDistrict::where('district_id', $this->district)->get()->map(
            fn($item) => ['value' => $item->id, 'label' => sprintf('%s (%s)', $item->th_name, $item->en_name)]
        )->toArray();
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
                          autocomplete="username" readonly/>
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

        <div>
            <x-input-label for="region" :value="__('Region')"/>
            <x-select-input wire:model.live="region" wire:change="getCitiesByRegion" id="region" name="region" class="mt-1 block w-full"
                            :options="$regions"
                            autofocus/>
            <x-input-error class="mt-2" :messages="$errors->get('region')"/>
        </div>

        <div>
            <x-input-label for="city" :value="__('City')"/>
            <x-select-input wire:model.live="city" wire:key="{{ $region }}" wire:change="getDistrictsByCity" id="city" name="city" class="mt-1 block w-full"
                            :options="$cities"
                            autofocus/>
            <x-input-error class="mt-2" :messages="$errors->get('city')"/>
        </div>

        <div>
            <x-input-label for="district" :value="__('District')"/>
            <x-select-input wire:model.live="district" wire:key="{{ $city }}" wire:change="getSubDistrictsByDistrict" id="district" name="district" class="mt-1 block w-full"
                            :options="$districts"
                            autofocus/>
            <x-input-error class="mt-2" :messages="$errors->get('district')"/>
        </div>

        <div>
            <x-input-label for="subDistrict" :value="__('Sub District')"/>
            <x-select-input wire:model="subDistrict" wire:key="{{ $district }}" id="subDistrict" name="subDistrict" class="mt-1 block w-full"
                            :options="$subDistricts"
                            autofocus/>
            <x-input-error class="mt-2" :messages="$errors->get('subDistrict')"/>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save And Continue Booking') }}</x-primary-button>

            <x-action-message class="me-3" on="profile-updated">
                {{ __('Profile Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>
