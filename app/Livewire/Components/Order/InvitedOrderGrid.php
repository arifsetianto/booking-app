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
class InvitedOrderGrid extends Component
{
    use WithPagination;

    public string $search = '';
    public string $searchBatch = '';

    public function mount(): void
    {
        $this->searchBatch = Batch::latest()->firstOrFail()->id;
    }

    /**
     * @return View|Application|Factory|\Illuminate\Contracts\Foundation\Application
     */
    public function render(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.pages.orders.invited-order-grid')->with([
            'orders' => Order::select('orders.*')
                             ->where('orders.status', OrderStatus::INVITED)
                             ->when($this->search !== '', function (Builder $query) {
                                 $query
                                     ->where(function ($qb) {
                                         $qb
                                             ->where('orders.code', 'LIKE', '%' . $this->search . '%')
                                             ->orWhere('orders.email', 'LIKE', '%' . $this->search . '%')
                                             ->orWhere('orders.name', 'LIKE', '%' . $this->search . '%')
                                             ->orWhere('orders.phone', 'LIKE', '%' . $this->search . '%')
                                             ->orWhere('orders.instagram', 'LIKE', '%' . $this->search . '%')
                                         ;
                                     });
                             })
                             ->when($this->searchBatch !== '', fn(Builder $query) => $query->where('orders.batch_id', $this->searchBatch))
                             ->with(['batch', 'source', 'shipping', 'payment'])
                             ->orderBy('orders.created_at')
                             ->paginate(10),
            'batches' => Batch::orderBy('number')->get(),
        ]);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function redirectToInviteOrderPage(): void
    {
        $this->redirectRoute('order.invite.create');
    }

    public function redirectToInviteExistingOrderPage(): void
    {
        $this->redirectRoute('order.invite-existing.create');
    }
}
