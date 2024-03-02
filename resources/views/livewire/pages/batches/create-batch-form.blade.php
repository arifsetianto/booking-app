<?php

use App\Models\Batch;
use App\ValueObject\BatchStatus;
use Livewire\Volt\Component;
use function Livewire\Volt\{state};

new class extends Component {
    public int $stock = 0;

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function save(): void
    {
        $this->validate(
            [
                'stock' => ['required', 'numeric', 'min:0'],
            ]
        );

        if (Batch::where('status', BatchStatus::PUBLISHED)->exists()) {
            session()->flash('error', 'Can only create 1 active batch.');
        } else {
            Batch::create($this->stock);

            session()->flash('message', 'New batch successfully created.');
        }

        $this->redirectIntended(default: '/batches', navigate: true);
    }
};

?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Create New Batch') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("To continue the booking process, please complete your profile data first.") }}
        </p>
    </header>

    <form wire:submit="save" class="mt-6 space-y-6">
        <div>
            <x-input-label for="stock" :value="__('Initial Stock')"/>
            <x-text-input wire:model="stock" id="stock" name="stock" type="number" min="0" class="mt-1 block w-full"
                          autofocus autocomplete="stock"/>
            <x-input-error class="mt-2" :messages="$errors->get('stock')"/>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save And Publish') }}</x-primary-button>
        </div>
    </form>
</section>
