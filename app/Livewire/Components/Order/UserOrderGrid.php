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

    public function mount(): void
    {
        $this->searchBatch = Batch::latest()->firstOrFail()->id;
    }

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
                                             ->where('code', 'LIKE', '%' . $this->searchKeyword . '%')
                                             ->orWhere('email', 'LIKE', '%' . $this->searchKeyword . '%')
                                             ->orWhere('name', 'LIKE', '%' . $this->searchKeyword . '%')
                                             ->orWhere('phone', 'LIKE', '%' . $this->searchKeyword . '%')
                                             ->orWhere('instagram', 'LIKE', '%' . $this->searchKeyword . '%')
                                         ;
                                     });
                             })
                             ->when($this->searchBatch !== '', fn(Builder $query) => $query->where('batch_id', $this->searchBatch))
                             ->when($this->searchStatus !== '', fn(Builder $query) => $query->where('status', $this->searchStatus))
                             ->with(['batch', 'source', 'shipping', 'payment'])
                             ->orderBy('created_at', 'desc')
                             ->paginate(10),
            'batches' => Batch::orderBy('number')->get(),
            'statuses' => OrderStatus::getOptions(),
        ]);
    }
}
