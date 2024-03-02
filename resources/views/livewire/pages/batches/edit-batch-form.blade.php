<?php

use App\Models\Batch;
use App\ValueObject\BatchStatus;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use function Livewire\Volt\{state};

new class extends Component {
    public string $batch = '';
    public int $stock = 0;
    public string $status = '';

    public array $statuses = [];

    public function mount(\Illuminate\Http\Request $request): void
    {
        $this->batch = $request->route('batch');

        $batch = Batch::find($this->batch);

        $this->stock = $batch->total_stock;
        $this->status = $batch->status->value;
        $this->statuses = BatchStatus::getOptions();
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function update(): void
    {
        $this->validate(
            [
                'stock'  => ['required', 'numeric', 'min:0'],
                'status' => ['required', 'string', Rule::in(BatchStatus::getValues())]
            ]
        );

        /** @var Batch $batch */
        $batch = Batch::find($this->batch);
        $batch->total_stock = $this->stock;
        $batch->status = BatchStatus::from($this->status);

        $batch->save();

        session()->flash('message', sprintf('Batch %s successfully updated.', $batch->number));

        $this->redirectIntended(default: '/batches', navigate: true);
    }
};

?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Edit Batch') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("To continue the booking process, please complete your profile data first.") }}
        </p>
    </header>

    <form wire:submit="update" class="mt-6 space-y-6">
        <div>
            <x-input-label for="stock" :value="__('Initial Stock')"/>
            <x-text-input wire:model="stock" id="stock" name="stock" type="number" min="0" class="mt-1 block w-full"
                          autofocus autocomplete="stock"/>
            <x-input-error class="mt-2" :messages="$errors->get('stock')"/>
        </div>

        <div>
            <x-input-label for="status" :value="__('Status')"/>
            <x-select-input wire:model="status" id="status" name="status" class="mt-1 block w-full"
                            :options="$statuses"
                            autofocus/>
            <x-input-error class="mt-2" :messages="$errors->get('status')"/>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Update') }}</x-primary-button>
        </div>
    </form>
</section>
