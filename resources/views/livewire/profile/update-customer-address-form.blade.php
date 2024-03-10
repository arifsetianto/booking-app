<?php

use App\Models\City;
use App\Models\District;
use App\Models\Region;
use App\Models\SubDistrict;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use function Livewire\Volt\{state};

new class extends Component {
    public ?string $address = '';
    public ?string $region = '';
    public ?string $city = '';
    public ?string $district = '';
    public ?string $subDistrict = '';

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

        $this->address = $user->profile->address;
        $this->region = $user->profile->subDistrict?->district?->city?->region?->id;
        $this->city = $user->profile->subDistrict?->district?->city?->id;
        $this->district = $user->profile->subDistrict?->district?->id;
        $this->subDistrict = $user->profile->subDistrict?->id;

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
    public function updateAddressInformation(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $this->validate(
            [
                'address'     => ['required', 'string'],
                'subDistrict' => ['required', 'uuid', 'exists:sub_districts,id']
            ]
        );

        $user->profile->address = $this->address;
        $user->profile->subDistrict()->associate(SubDistrict::find($this->subDistrict));

        $user->profile->save();

        $this->dispatch('address-updated', name: $user->name);
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
};

?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Address Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your address information.") }}
        </p>
    </header>

    <form wire:submit="updateAddressInformation" class="mt-6 space-y-6">
        <div>
            <x-input-label for="address" :value="__('Address')"/>
            <x-text-area wire:model="address" id="address" name="address" class="mt-1 block w-full"
                         autofocus autocomplete="address"/>
            <x-input-error class="mt-2" :messages="$errors->get('address')"/>
        </div>

        <div>
            <x-input-label for="region" :value="__('Region')"/>
            <x-select-input wire:model.live="region" wire:change="getCitiesByRegion" id="region" name="region"
                            class="mt-1 block w-full"
                            :options="$regions"
                            autofocus/>
            <x-input-error class="mt-2" :messages="$errors->get('region')"/>
        </div>

        <div>
            <x-input-label for="city" :value="__('City')"/>
            <x-select-input wire:model.live="city" wire:key="{{ $region }}" wire:change="getDistrictsByCity" id="city"
                            name="city" class="mt-1 block w-full"
                            :options="$cities"
                            autofocus/>
            <x-input-error class="mt-2" :messages="$errors->get('city')"/>
        </div>

        <div>
            <x-input-label for="district" :value="__('District')"/>
            <x-select-input wire:model.live="district" wire:key="{{ $city }}" wire:change="getSubDistrictsByDistrict"
                            id="district" name="district" class="mt-1 block w-full"
                            :options="$districts"
                            autofocus/>
            <x-input-error class="mt-2" :messages="$errors->get('district')"/>
        </div>

        <div>
            <x-input-label for="subDistrict" :value="__('Sub District')"/>
            <x-select-input wire:model="subDistrict" wire:key="{{ $district }}" id="subDistrict" name="subDistrict"
                            class="mt-1 block w-full"
                            :options="$subDistricts"
                            autofocus/>
            <x-input-error class="mt-2" :messages="$errors->get('subDistrict')"/>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save Address') }}</x-primary-button>

            <x-action-message class="me-3" on="address-updated">
                {{ __('Address Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>
