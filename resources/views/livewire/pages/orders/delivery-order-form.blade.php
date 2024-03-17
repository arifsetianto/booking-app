<?php

use App\Event\Order\OrderPurchased;
use App\Livewire\Forms\Order\CreateDeliveryOrderForm;
use App\Models\Batch;
use App\Models\City;
use App\Models\District;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Region;
use App\Models\Shipping;
use App\Models\SubDistrict;
use App\Models\User;
use App\ValueObject\OrderStatus;
use App\ValueObject\PaymentStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Volt\Component;
use function Livewire\Volt\{state};

new class extends Component {
    public CreateDeliveryOrderForm $form;
    public Order $order;

    public array $regions = [];
    public array $cities = [];
    public array $districts = [];
    public array $subDistricts = [];

    public function mount(Request $request): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->order = Order::findOrFail($request->route('order'));

        $this->form->name = $this->order->orderItem->receiver_en_name;
        //$this->form->phone = $user->profile->phone;
        //$this->form->address = $user->profile->address;
        //$this->form->region = $user->profile->subDistrict?->district?->city?->region?->id;
        //$this->form->city = $user->profile->subDistrict?->district?->city?->id;
        //$this->form->district = $user->profile->subDistrict?->district?->id;
        //$this->form->subDistrict = $user->profile->subDistrict?->id;

        $this->form->fee = 0;
        $this->regions = Region::get()->map(fn($item) => ['value' => $item->id, 'label' => sprintf('%s (%s)', $item->th_name, $item->en_name)])->toArray();

        //if ($user->profile->subDistrict?->district?->city) {
        //    $this->getCitiesByRegion();
        //}

        //if ($user->profile->subDistrict?->district) {
        //    $this->getDistrictsByCity();
        //}

        //if ($user->profile->subDistrict) {
        //    $this->getSubDistrictsByDistrict();
        //}
    }

    public function getCitiesByRegion(): void
    {
        $this->cities = City::where('region_id', $this->form->region)->get()->map(
            fn($item) => ['value' => $item->id, 'label' => sprintf('%s (%s)', $item->th_name, $item->en_name)]
        )->toArray();
    }

    public function getDistrictsByCity(): void
    {
        $this->districts =
            District::where('city_id', $this->form->city)->get()->map(
                fn($item) => ['value' => $item->id, 'label' => sprintf('%s (%s)', $item->th_name, $item->en_name)]
            )->toArray();
    }

    public function getSubDistrictsByDistrict(): void
    {
        $this->subDistricts = SubDistrict::where('district_id', $this->form->district)->get()->map(
            fn($item) => ['value' => $item->id, 'label' => sprintf('%s (%s)', $item->th_name, $item->en_name)]
        )->toArray();
    }

    public function selectSubDistrict(): void
    {
        $this->form->fee = 100;
    }

    public function payOrder(): void
    {
        $this->form->validate();

        /** @var Batch $batch */
        $batch = Batch::findOrFail($this->order->batch->id);

        if ($batch->getAvailableStock() < 1) {
            Session::flash('error', 'Stock is not available.');

            $this->redirectRoute(name: 'orders.list');
        } else {
            /** @var Order $order */
            $order = Order::findOrFail($this->order->id);
            $order->status = OrderStatus::PENDING;
            $order->amount = $order->amount + $this->form->fee;

            $order->save();

            $shipping = new Shipping();
            $shipping->order()->associate($order);
            $shipping->name = $this->form->name;
            $shipping->phone = $this->form->phone;
            $shipping->address = $this->form->address;
            $shipping->subDistrict()->associate(SubDistrict::findOrFail($this->form->subDistrict));
            $shipping->fee = $this->form->fee;

            $shipping->save();

            $payment = new Payment();
            $payment->order()->associate($order);
            $payment->status = PaymentStatus::PENDING;
            $payment->expired_at = Carbon::now()->addMinutes(30);

            $payment->save();

            event(new OrderPurchased($order));

            $this->redirectRoute(name: 'orders.payment', parameters: ['order' => $order->id]);
        }
    }

    public function cancelOrder(): void
    {
        /** @var Order $order */
        $order = Order::findOrFail($this->order->id);
        $order->status = OrderStatus::CANCELED;
        $order->canceled_at = Carbon::now();

        $order->save();

        Session::flash('message', sprintf('Order #%s has been successfully canceled.', $this->order->code));

        $this->redirectRoute(name: 'orders.list', parameters: ['order' => $this->order->id]);
    }

    public function backToPrevious(): void
    {
        $this->redirectRoute(name: 'orders.edit', parameters: ['order' => $this->order->id]);
    }
};

?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Delivery Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Please fill in the shipping information correctly so that we can send the order immediately.") }}
        </p>
    </header>

    <div class="mt-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="name" :value="__('Receiver Name same as ID')"/>
                <x-text-input wire:model="form.name" id="name" name="name" type="text"
                              class="mt-1 block w-full"
                              autofocus readonly autocomplete="name"
                              placeholder="Please enter receiver name same as ID"/>
                <x-input-error class="mt-2" :messages="$errors->get('form.name')"/>
            </div>
            <div>
                <x-input-label for="phone" :value="__('Receiver Phone')"/>
                <x-text-input wire:model="form.phone" id="phone" name="phone" type="text"
                              class="mt-1 block w-full"
                              autofocus autocomplete="phone"
                              placeholder="Please enter receiver phone"/>
                <x-input-error class="mt-2" :messages="$errors->get('form.phone')"/>
            </div>
            <div>
                <x-input-label for="address" :value="__('Soi (Street Address)')"/>
                <x-text-area wire:model="form.address" id="address" name="address" class="mt-1 block w-full"
                             autofocus autocomplete="address"/>
                <x-input-error class="mt-2" :messages="$errors->get('form.address')"/>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="region" :value="__('Region')"/>
                <x-select-input wire:model.live="form.region" wire:change="getCitiesByRegion" id="region" name="region"
                                class="mt-1 block w-full"
                                :options="$regions"
                                autofocus/>
                <x-input-error class="mt-2" :messages="$errors->get('form.region')"/>
            </div>
            <div>
                <x-input-label for="city" :value="__('Province')"/>
                <x-select-input wire:model.live="form.city" wire:key="{{ $form->region }}"
                                wire:change="getDistrictsByCity"
                                id="city" name="city" class="mt-1 block w-full"
                                :options="$cities"
                                autofocus/>
                <x-input-error class="mt-2" :messages="$errors->get('form.city')"/>
            </div>
            <div>
                <x-input-label for="district" :value="__('Amphoe (District)')"/>
                <x-select-input wire:model.live="form.district" wire:key="{{ $form->city }}"
                                wire:change="getSubDistrictsByDistrict" id="district" name="district"
                                class="mt-1 block w-full"
                                :options="$districts"
                                autofocus/>
                <x-input-error class="mt-2" :messages="$errors->get('form.district')"/>
            </div>
            <div>
                <x-input-label for="subDistrict" :value="__('Tambon (Sub-District)')"/>
                <x-select-input wire:model="form.subDistrict" wire:key="{{ $form->district }}"
                                wire:change="selectSubDistrict"
                                id="subDistrict" name="subDistrict"
                                class="mt-1 block w-full"
                                :options="$subDistricts"
                                autofocus/>
                <x-input-error class="mt-2" :messages="$errors->get('form.subDistrict')"/>
            </div>
            <div>
                <x-input-label for="delivery_fee" :value="__('Delivery & Service Fee')"/>
                <div class="flex mt-1 w-full">
                  <span
                      class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border rounded-e-0 border-gray-300 rounded-s-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                    THB
                  </span>
                    <x-text-input wire:model="form.fee" id="delivery_fee" name="delivery_fee" type="text"
                                  class="block w-full rounded-none rounded-e-lg bg-gray-50 border text-gray-900 flex-1 min-w-0 text-sm border-gray-300 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                  autofocus autocomplete="delivery_fee" readonly/>
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('form.fee')"/>
            </div>
        </div>
        <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="qty" :value="__('Quantity')"/>
                <p class="text-sm mt-1 text-gray-500">1 pcs ThaiQuran (750gr)</p>
            </div>
            <div>
                <x-input-label for="price" :value="__('Price')"/>
                <span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">FREE</span>
            </div>
        </div>

        <div class="flex items-center gap-4 pt-5">
            <x-primary-button x-data=""
                              x-on:click.prevent="$dispatch('open-modal', 'confirm-order-payment')">{{ __('Pay Now!') }}</x-primary-button>
            <x-secondary-button wire:click="backToPrevious">{{ __('Edit Booking') }}</x-secondary-button>
            <x-danger-button x-data=""
                             x-on:click.prevent="$dispatch('open-modal', 'confirm-order-cancellation')">{{ __('Cancel Booking') }}</x-danger-button>
        </div>
    </div>

    <x-modal name="confirm-order-payment" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="payOrder" class="p-6">
            <div class="relative bg-white rounded-lg dark:bg-gray-700">
                <button type="button"
                        class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        x-on:click="$dispatch('close')">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
                <div class="p-4 md:p-5 text-center">
                    <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true"
                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                    <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to
                        pay this order?</h3>
                    <x-primary-button
                        class="text-white bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                        {{ __('Yes, Pay Now!') }}
                    </x-primary-button>
                    <x-secondary-button x-on:click="$dispatch('close')"
                                        class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                        {{ __('No, Cancel') }}
                    </x-secondary-button>
                </div>
            </div>
        </form>
    </x-modal>

    <x-modal name="confirm-order-cancellation" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="cancelOrder" class="p-6">
            <div class="relative bg-white rounded-lg dark:bg-gray-700">
                <button type="button"
                        class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        x-on:click="$dispatch('close')">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
                <div class="p-4 md:p-5 text-center">
                    <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true"
                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                    <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to
                        cancel this order?</h3>
                    <x-primary-button
                        class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                        {{ __('Yes, Cancel Now!') }}
                    </x-primary-button>
                    <x-secondary-button x-on:click="$dispatch('close')"
                                        class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                        {{ __('Not Now') }}
                    </x-secondary-button>
                </div>
            </div>
        </form>
    </x-modal>
</section>
