<?php

declare(strict_types=1);

namespace App\Livewire\Components\Order;

use App\Event\Order\OrderCompleted;
use App\Livewire\Forms\Order\CompleteOrderForm;
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
use Livewire\WithPagination;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class CompletedOrderGrid extends Component
{
    use WithPagination;

    public string $searchKeyword = '';
    public string $searchBatch = '';
    public ?Order $selectedOrder = null;
    public CompleteOrderForm $form;

    /**
     * @return View|Application|Factory|\Illuminate\Contracts\Foundation\Application
     */
    public function render(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.pages.orders.completed-order-grid')->with([
            'orders' => Order::where('status', OrderStatus::VERIFIED)
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
                             ->with(['batch', 'source', 'shipping', 'payment'])
                             ->orderBy('created_at')
                             ->paginate(10),
            'batches' => Batch::orderBy('number')->get(),
        ]);
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
}
