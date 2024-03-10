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
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class UserOrderGrid extends Component
{
    use WithPagination;

    public string $searchKeyword = '';
    public string $searchBatch = '';
    public string $searchStatus = '';

    /**
     * @return View|Application|Factory|\Illuminate\Contracts\Foundation\Application
     */
    public function render(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.pages.orders.user-order-grid')->with([
            'orders' => Order::where('user_id', Auth::user()->getAuthIdentifier())
                             ->when($this->searchKeyword !== '', function (Builder $query) {
                                 $query
                                     ->where(function ($qb) {
                                         $qb
                                             ->where('code', 'ilike', '%' . $this->searchKeyword . '%')
                                             ->orWhere('email', 'ilike', '%' . $this->searchKeyword . '%')
                                             ->orWhere('name', 'ilike', '%' . $this->searchKeyword . '%')
                                             ->orWhere('phone', 'ilike', '%' . $this->searchKeyword . '%')
                                             ->orWhere('instagram', 'ilike', '%' . $this->searchKeyword . '%')
                                         ;
                                     });
                             })
                             ->when($this->searchBatch !== '', fn(Builder $query) => $query->where('batch_id', $this->searchBatch))
                             ->when($this->searchStatus !== '', fn(Builder $query) => $query->where('status', $this->searchStatus))
                             ->with(['batch', 'source', 'shipping', 'payment'])
                             ->orderBy('created_at')
                             ->paginate(10),
            'batches' => Batch::orderBy('number')->get(),
            'statuses' => OrderStatus::getOptions(),
        ]);
    }
}
