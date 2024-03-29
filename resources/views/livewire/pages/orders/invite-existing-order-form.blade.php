<?php

use App\Event\Order\OrderInvited;
use App\Livewire\Forms\Order\InviteExistingOrderForm;
use App\Models\Batch;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Shipping;
use App\Models\User;
use App\ValueObject\OrderStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Livewire\Volt\Component;
use function Livewire\Volt\{state};

new class extends Component {
    public array $batches = [];
    public bool $isShowUserOrdersTable = false;
    public Collection $orders;
    public ?Order $selectedOrder = null;

    public InviteExistingOrderForm $form;

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
            $this->selectedOrder = Order::findOrFail($this->form->order);

            $userOrderCount = null !== $user ? Order::where('user_id', $user->id)->count() : 0;
            $order = new Order();
            $order->email = $this->selectedOrder?->email ?? $this->form->email;
            $order->phone = $this->selectedOrder?->user?->profile?->phone ?? null;
            $order->name = $this->selectedOrder?->name ?? null;
            $order->instagram = $this->selectedOrder?->user?->profile?->instagram ?? null;
            $order->batch()->associate($batch);
            $order->status = OrderStatus::INVITED;
            $order->code = $this->createUniqueOrderCode();
            $order->qty = 1;
            $order->amount = 0;
            $order->user_order_sequence = $userOrderCount + 1;
            $order->comment = $this->selectedOrder?->comment;
            $order->reference()->associate($this->selectedOrder);

            if (null !== $user) {
                $order->user()->associate($user);

                if (null !== $this->selectedOrder?->user?->profile?->source) {
                    $order->source()->associate($this->selectedOrder->user->profile->source);
                }
            }

            $order->save();

            if ($this->selectedOrder->orderItem) {
                $item = new OrderItem();
                $item->receiver_en_name = $this->selectedOrder?->orderItem?->receiver_en_name;
                $item->receiver_th_name = $this->selectedOrder?->orderItem?->receiver_th_name;
                $item->qty = 1;
                $item->amount = 0;
                $item->gender = $this->selectedOrder?->orderItem?->gender;
                $item->order()->associate($order);
                $item->identity_file = $this->selectedOrder?->orderItem?->identity_file;

                if ($this->selectedOrder?->orderItem?->religion) {
                    $item->religion()->associate($this->selectedOrder->orderItem->religion);
                }

                if ($this->selectedOrder?->orderItem?->designation) {
                    $item->designation()->associate($this->selectedOrder->orderItem->designation);
                }

                $item->save();
            }

            if ($this->selectedOrder->shipping) {
                $shipping = new Shipping();
                $shipping->order()->associate($order);
                $shipping->name = $this->selectedOrder?->shipping?->name;
                $shipping->phone = $this->selectedOrder?->shipping?->phone;
                $shipping->address = $this->selectedOrder?->shipping?->address;
                $shipping->fee = $this->selectedOrder?->shipping?->fee;

                if ($this->selectedOrder?->shipping?->subDistrict) {
                    $shipping->subDistrict()->associate($this->selectedOrder?->shipping?->subDistrict);
                }

                $shipping->save();
            }

            event(new OrderInvited(order: $order, useReference: true));

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

    public function searchUser(): void
    {
        $user = User::where('email', $this->form->email)->first();

        if (null !== $user) {
            $this->orders = Order::where('user_id', $user->id)->get();
            $this->isShowUserOrdersTable = true;
        } else {
            $this->isShowUserOrdersTable = false;
        }
    }

    public function showModalDetail(string $orderId): void
    {
        $this->selectedOrder = Order::findOrFail($orderId);

        $this->dispatch('open-modal', 'modal-order-detail');
    }
}

?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Existing Invitation Form') }}
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
            <button wire:click="searchUser" type="button"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                     class="w-3.5 h-3.5 me-2">
                    <path fill-rule="evenodd"
                          d="M10.5 3.75a6.75 6.75 0 1 0 0 13.5 6.75 6.75 0 0 0 0-13.5ZM2.25 10.5a8.25 8.25 0 1 1 14.59 5.28l4.69 4.69a.75.75 0 1 1-1.06 1.06l-4.69-4.69A8.25 8.25 0 0 1 2.25 10.5Z"
                          clip-rule="evenodd"/>
                </svg>
                {{ __('Search Order') }}
            </button>
        </div>

        @if($isShowUserOrdersTable)
            <div>
                <div class="relative overflow-x-auto sm:rounded-lg">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <caption
                            class="p-5 text-lg font-semibold text-left rtl:text-right text-gray-900 bg-white dark:text-white dark:bg-gray-800">
                            User Orders
                            <p class="mt-1 text-sm font-normal text-gray-500 dark:text-gray-400">The following is a list
                                of orders from the searched users.</p>
                        </caption>
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="p-4">
                                <div class="flex items-center">
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Code
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Order ke
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Receiver
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Zip Code
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <span class="sr-only">Select</span>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($orders as $order)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="w-4 p-4">
                                    <div class="flex items-center">
                                        <input wire:model="form.order" id="radio-{{ $order->id }}"
                                               name="order-{{ $order->id }}" value="{{ $order->id }}" type="radio"
                                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <label for="radio-{{ $order->id }}" class="sr-only">checkbox</label>
                                    </div>
                                </td>
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    #{{ $order->code }}
                                </th>
                                <td class="px-6 py-4">
                                    {{ $order->user_order_sequence ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($order->orderItem)
                                        {{ $order->orderItem->receiver_th_name }}
                                        ({{ $order->orderItem->receiver_en_name }})
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    {{ $order->shipping?->subDistrict?->zip_code ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="{{ $order->status->is(OrderStatus::CANCELED) || $order->status->is(OrderStatus::REJECTED) ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' }} text-xs font-medium me-2 px-2.5 py-0.5 rounded-full">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a wire:click="showModalDetail('{{ $order->id }}')"
                                       class="font-medium text-blue-600 dark:text-blue-500 cursor-pointer hover:underline">View
                                        Detail</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('form.order')"/>

            <div class="flex items-center gap-4">
                <x-primary-button>{{ __('Invite New Order') }}</x-primary-button>
            </div>
        @endif
    </form>

    @if($selectedOrder)
        <x-modal name="modal-order-detail" :show="$errors->isNotEmpty()" focusable>
            <div class="relative p-6 w-full max-w-7xl max-h-full">
                <header>
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Detail Order #:code', ['code' => $selectedOrder->code]) }}
                            </h2>
                        </div>
                        <div>
                            <span
                                class="{{ $selectedOrder->status->is(OrderStatus::CANCELED) || $selectedOrder->status->is(OrderStatus::REJECTED) ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' }} text-xs font-medium me-2 px-2.5 py-0.5 rounded-full">
                                {{ $selectedOrder->status }}
                            </span>
                        </div>
                    </div>
                </header>
                <div class="mt-10">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Full Name</p>
                            <p class="pt-1 text-sm">{{ $selectedOrder->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Email</p>
                            <p class="pt-1 text-sm">{{ $selectedOrder->email ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Phone</p>
                            <p class="pt-1 text-sm">{{ $selectedOrder->phone ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Instagram</p>
                            <p class="pt-1 text-sm">{{ $selectedOrder->instagram ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">How do you know us</p>
                            <p class="pt-1 text-sm">{{ $selectedOrder->source?->name ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4 mt-9">
                        <div>
                            <p class="text-sm text-gray-500">Comment</p>
                            <p class="pt-1 text-sm">{{ $selectedOrder->comment ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-9">
                        <div>
                            <p class="text-sm text-gray-500">Receiver Name (in English)</p>
                            <p class="pt-1 text-sm">{{ $selectedOrder->orderItem?->receiver_en_name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Receiver Name (in Thai)</p>
                            <p class="pt-1 text-sm">{{ $selectedOrder->orderItem?->receiver_th_name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Order Date</p>
                            <p class="pt-1 text-sm">{{ $selectedOrder->orderItem?->created_at?->format('d-m-Y H:i:s') ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Order For</p>
                            <p class="pt-1 text-sm">{{ $selectedOrder->orderItem?->designation?->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Gender</p>
                            <p class="pt-1 text-sm capitalize">{{ $selectedOrder->orderItem?->gender ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Religion</p>
                            <p class="pt-1 text-sm">{{ $selectedOrder->orderItem?->religion?->name ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-9">
                        <div>
                            <p class="text-sm text-gray-500">Receiver Name</p>
                            <p class="pt-1 text-sm">{{ $selectedOrder->shipping?->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Receiver Mobile No.</p>
                            <p class="pt-1 text-sm">{{ $selectedOrder->shipping?->phone ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Address</p>
                            <p class="pt-1 text-sm">{{ $selectedOrder->shipping?->address ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Region</p>
                            <p class="pt-1 text-sm">
                                @if($selectedOrder->shipping)
                                    {{ $selectedOrder->shipping?->subDistrict?->district->city->region->th_name }}
                                    ({{ $selectedOrder->shipping?->subDistrict?->district->city->region->en_name }})
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Province</p>
                            <p class="pt-1 text-sm">
                                @if($selectedOrder->shipping)
                                    {{ $selectedOrder->shipping->subDistrict->district->city->th_name }}
                                    ({{ $selectedOrder->shipping->subDistrict->district->city->en_name }})
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">District</p>
                            <p class="pt-1 text-sm">
                                @if($selectedOrder->shipping)
                                    {{ $selectedOrder->shipping->subDistrict->district->th_name }}
                                    ({{ $selectedOrder->shipping->subDistrict->district->en_name }})
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Sub District</p>
                            <p class="pt-1 text-sm">
                                @if($selectedOrder->shipping)
                                    {{ $selectedOrder->shipping->subDistrict->th_name }}
                                    ({{ $selectedOrder->shipping->subDistrict->en_name }})
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Zip Code</p>
                            <p class="pt-1 text-sm">{{ $selectedOrder->shipping?->subDistrict?->zip_code ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tracking Code</p>
                            <p class="pt-1 text-sm">
                                @if($selectedOrder->shipping?->tracking_code)
                                    <a href="https://track.thailandpost.com/?trackNumber={{ $selectedOrder->shipping->tracking_code }}"
                                       target="_blank" class="hover:underline">
                                        {{ $selectedOrder->shipping->tracking_code ?? '-' }}
                                    </a>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4 mt-9">
                        <div class="text-center">
                            <figure class="max-w-7xl">
                                <img class="h-auto max-w-sm mx-auto rounded-lg"
                                     src="{{ $selectedOrder->orderItem?->identity_file ? Storage::url($selectedOrder->orderItem->identity_file) : asset('images/image-default.jpg') }}"
                                     alt="">
                                <figcaption class="mt-2 text-sm text-center text-gray-500 dark:text-gray-400">Receiver
                                    Thai ID
                                </figcaption>
                            </figure>
                        </div>
                    </div>
                </div>
            </div>
        </x-modal>
    @endif
</section>
