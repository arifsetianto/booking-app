<?php

declare(strict_types=1);

namespace App\Livewire\Components\Order;

use App\Event\Order\OrderCompleted;
use App\Imports\OrdersVerifiedImport;
use App\Livewire\Forms\Order\CompleteOrderForm;
use App\Livewire\Forms\Order\ImportOrderForm;
use App\Models\Batch;
use App\Models\Order;
use App\ValueObject\OrderStatus;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class CompletedOrderGrid extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public string $searchBatch = '';
    public ?Order $selectedOrder = null;
    public CompleteOrderForm $form;
    public ImportOrderForm $importOrderForm;

    public function mount(): void
    {
        $this->searchBatch = Batch::latest()->firstOrFail()->id;
    }

    /**
     * @return View|Application|Factory|\Illuminate\Contracts\Foundation\Application
     */
    public function render(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.pages.orders.completed-order-grid')->with([
            'orders' => Order::select('orders.*')
                             ->join('order_items', 'orders.id', '=', 'order_items.order_id')
//                             ->join('shippings', 'orders.id', '=', 'shippings.order_id')
//                             ->join('sub_districts', 'shippings.sub_district_id', '=', 'sub_districts.id')
                             ->where('orders.status', OrderStatus::VERIFIED)
                             ->when($this->search !== '', function (Builder $query) {
                                 $query
                                     ->where(function ($qb) {
                                         $qb
                                             ->where('orders.code', 'LIKE', '%' . $this->search . '%')
                                             ->orWhere('orders.email', 'LIKE', '%' . $this->search . '%')
                                             ->orWhere('orders.name', 'LIKE', '%' . $this->search . '%')
                                             ->orWhere('orders.phone', 'LIKE', '%' . $this->search . '%')
                                             ->orWhere('orders.instagram', 'LIKE', '%' . $this->search . '%')
                                             ->orWhere('order_items.receiver_th_name', 'LIKE', '%' . $this->search . '%')
                                             ->orWhere('order_items.receiver_en_name', 'LIKE', '%' . $this->search . '%')
                                             ->orWhereHas('shipping', function (Builder $query) {
                                                 $query->where('shippings.phone', 'LIKE', '%' . $this->search . '%');
                                                 $query->orWhere('shippings.name', 'LIKE', '%' . $this->search . '%');
                                                 $query->orWhereHas('subDistrict', function (Builder $query) {
                                                     $query->where('sub_districts.zip_code', 'LIKE', '%' . $this->search . '%');
                                                 });
                                             })
//                                             ->orWhere('shippings.phone', 'LIKE', '%' . $this->searchKeyword . '%')
//                                             ->orWhere('sub_districts.zip_code', 'LIKE', '%' . $this->searchKeyword . '%')
                                         ;
                                     });
                             })
                             ->when($this->searchBatch !== '', fn(Builder $query) => $query->where('orders.batch_id', $this->searchBatch))
                             ->with(['batch', 'source', 'shipping', 'payment'])
                             ->orderBy('orders.verified_at')
                             ->paginate(10),
            'batches' => Batch::orderBy('number')->get(),
        ]);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function selectOrder(string $orderId): void
    {
        $this->selectedOrder = Order::findOrFail($orderId);
        $this->dispatch('open-modal', 'confirm-order-completion');
    }

    public function completeOrder(): void
    {
        $this->form->validate();

        $this->selectedOrder->status = OrderStatus::COMPLETED;
        $this->selectedOrder->completed_at = Carbon::now();

        $this->selectedOrder->shipping->tracking_code = $this->form->trackingCode;

        $this->selectedOrder->save();
        $this->selectedOrder->shipping->save();

        event(new OrderCompleted($this->selectedOrder));

        Session::flash('message', sprintf('Order #%s has been completed.', $this->selectedOrder->code));

        $this->redirectRoute(name: 'order.list-complete');
    }

    public function exportData(): void
    {
        $this->redirectRoute(name: 'orders.verified.export');
    }

    public function importData(): void
    {
        $this->importOrderForm->validate();

        Excel::queueImport(new OrdersVerifiedImport, $this->importOrderForm->importFile);

        Session::flash('message', 'Orders data successfully imported.');

        $this->redirectRoute(name: 'order.list-complete');
    }
}
