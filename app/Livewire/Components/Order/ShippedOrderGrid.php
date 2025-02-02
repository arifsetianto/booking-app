<?php

declare(strict_types=1);

namespace App\Livewire\Components\Order;

use App\Models\Batch;
use App\Models\Order;
use App\ValueObject\OrderStatus;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Application;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class ShippedOrderGrid extends Component
{
    use WithPagination;

    public string $search = '';
    public string $searchBatch = '';
    public string $searchStatus = '';

    public function mount(): void
    {
        $this->searchBatch = Batch::latest()->firstOrFail()->id;
    }

    /**
     * @return View|Application|Factory|\Illuminate\Contracts\Foundation\Application
     */
    public function render(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.pages.orders.shipped-order-grid')->with([
            'orders' => Order::select('orders.*')
                             ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                             ->whereIn('orders.status', [OrderStatus::COMPLETED])
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
                                                 $query->orWhere('shippings.tracking_code', 'LIKE', '%' . $this->search . '%');
                                                 $query->orWhereHas('subDistrict', function (Builder $query) {
                                                     $query->where('sub_districts.zip_code', 'LIKE', '%' . $this->search . '%');
                                                 });
                                             })
                                         ;
                                     });
                             })
                             ->when($this->searchBatch !== '', fn(Builder $query) => $query->where('orders.batch_id', $this->searchBatch))
                             ->when($this->searchStatus !== '', fn(Builder $query) => $query->where('orders.status', $this->searchStatus))
                             ->with(['batch', 'source', 'shipping', 'payment'])
                             ->orderByDesc('orders.completed_at')
                             ->paginate(10),
            'batches' => Batch::orderBy('number')->get(),
            'statuses' => OrderStatus::getOptions(),
        ]);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
}
