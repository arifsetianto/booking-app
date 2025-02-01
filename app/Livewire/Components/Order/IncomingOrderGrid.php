<?php

declare(strict_types=1);

namespace App\Livewire\Components\Order;

use App\Models\Batch;
use App\Models\Order;
use App\ValueObject\OrderStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class IncomingOrderGrid extends Component
{
    use WithPagination;

    public string $search = '';
    public string $searchBatch = '';

    /**
     * @return View|Application|Factory|\Illuminate\Contracts\Foundation\Application
     */
    public function render(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.pages.orders.incoming-order-grid')->with([
            'orders' => Order::select('orders.*')
                             ->join('order_items', 'orders.id', '=', 'order_items.order_id')
//                             ->join('shippings', 'orders.id', '=', 'shippings.order_id')
//                             ->join('sub_districts', 'shippings.sub_district_id', '=', 'sub_districts.id')
                             ->where('orders.status', OrderStatus::CONFIRMED)
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
                             ->orderBy('orders.confirmed_at')
                             ->paginate(10),
            'batches' => Batch::orderBy('number')->get(),
        ]);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
}
