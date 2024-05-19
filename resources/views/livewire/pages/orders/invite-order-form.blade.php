<?php

use App\Event\Order\OrderInvited;
use App\Livewire\Forms\Order\InviteOrderForm;
use App\Models\Batch;
use App\Models\Order;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use App\ValueObject\OrderStatus;
use App\ValueObject\UserStatus;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Livewire\Volt\Component;
use function Livewire\Volt\{state};

new class extends Component {
    public array $batches = [];

    public InviteOrderForm $form;

    public function mount(): void
    {
        $this->batches = Batch::get()->map(fn($item) => ['value' => $item->id, 'label' => $item->number])->toArray();
    }

    public function invite(): void
    {
        $this->form->validate();

        /** @var Batch $batch */
        $batch = Batch::findOrFail($this->form->batch);

        if (!$batch || $batch->getAvailableStock() <= 0) {
            Session::flash('error', 'Unable to place orders due to out-of-stock.');

            $this->redirectRoute('order.list-invited');
        } else {
            /** @var User $user */
            $user = User::where(DB::raw('lower(email)'), strtolower($this->form->email))->first();

            if (null === $user) {
                event(
                    new Registered($user = User::create(['email' => $this->form->email, 'status' => UserStatus::NEW]))
                );
                $user->roles()->attach(Role::where('name', 'customer')->first());
                $user->profile()->associate(Profile::create());

                $user->save();
            }

            $userOrderCount = null !== $user ? Order::where('user_id', $user->id)->count() : 0;
            $order = new Order();
            $order->email = $user?->email ?? $this->form->email;
            $order->phone = $user?->profile?->phone ?? null;
            $order->name = $user?->name ?? null;
            $order->instagram = $user?->profile?->instagram ?? null;
            $order->batch()->associate($batch);
            $order->status = OrderStatus::INVITED;
            $order->code = $this->createUniqueOrderCode();
            $order->qty = 1;
            $order->amount = $this->generateUniqueAmount($batch);
            $order->user_order_sequence = $userOrderCount + 1;

            if (null !== $user) {
                $order->user()->associate($user);

                if (null !== $user->profile?->source) {
                    $order->source()->associate($user->profile->source);
                }
            }

            $order->save();

            event(new OrderInvited(order: $order));

            Session::flash('message', sprintf('New order %s has been successfully invited.', $order->code));

            $this->redirectRoute(name: 'order.list-invited');
        }
    }

    private function generateCode(): string
    {
        $date = now()->format('ymd');
        $seq =
            sprintf(
                "%'.04d",
                Order::whereYear('created_at', date('Y'))
                     ->whereMonth('created_at', date('n'))
                     ->whereDay('created_at', date('d'))
                     ->count() + 1
            );

        return $date . $seq;
    }

    private function createUniqueOrderCode(): string
    {
        return DB::transaction(
            function () {
                DB::table('orders')->lockForUpdate()->get();

                return $this->generateCode();
            }
        );
    }

    private function generateUniqueAmount(Batch $batch): float
    {
        $lastOrder = Order::where('batch_id', $batch->id)->orderBy('code', 'desc')->first();

        if ($lastOrder) {
            $lastAmount = $lastOrder->amount;
            $nextAmount = $lastAmount + 0.01;
        } else {
            $nextAmount = 100.00; // Initial amount
        }

        return round($nextAmount, 2);
    }
}

?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('New Invitation Form') }}
        </h2>
    </header>

    <form wire:submit="invite" class="mt-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="email" :value="__('Email Address')" class="required"/>
                <x-text-input wire:model="form.email" id="email" name="email" type="text"
                              class="mt-1 block w-full"
                              autofocus autocomplete="email"
                              placeholder="Please enter customer email address"/>
                <x-input-error class="mt-2" :messages="$errors->get('form.email')"/>
            </div>
            <div>
                <x-input-label for="batch" :value="__('Batch')" class="required"/>
                <x-select-input wire:model="form.batch" id="batch" name="batch" class="mt-1 block w-full"
                                :options="$batches"
                                autofocus/>
                <x-input-error class="mt-2" :messages="$errors->get('form.batch')"/>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Invite Now') }}</x-primary-button>
        </div>
    </form>
</section>
