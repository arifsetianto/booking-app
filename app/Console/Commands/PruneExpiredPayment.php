<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Event\Order\OrderCanceled;
use App\Models\Order;
use App\Models\Payment;
use App\ValueObject\OrderStatus;
use App\ValueObject\PaymentStatus;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class PruneExpiredPayment extends Command
{
    protected $signature = 'payment:prune-expired';

    protected $description = 'Prune expired payments';

    public function handle(): void
    {
        DB::transaction(function () {
            try {
                /** @var Payment $payment */
                foreach ($this->getQuery() as $payment) {
                    $payment->status = PaymentStatus::EXPIRED;
                    $payment->save();

                    /** @var Order $order */
                    $order = Order::findOrFail($payment->order->id);
                    $order->status = OrderStatus::CANCELED;
                    $order->canceled_at = Carbon::now();

                    $order->save();

                    event(new OrderCanceled($order));
                }
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        });
    }

    public function getQuery(): LazyCollection
    {
        return DB::table('payments')
                 ->where('status', operator: PaymentStatus::PENDING)
                 ->where('expired_at', '<', now())
                 ->whereNotNull('expired_at')
                 ->cursor();
    }
}
