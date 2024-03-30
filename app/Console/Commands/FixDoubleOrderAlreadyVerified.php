<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Shipping;
use App\ValueObject\OrderStatus;
use App\ValueObject\PaymentStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class FixDoubleOrderAlreadyVerified extends Command
{
    protected $signature = 'order:verified:fix-double';

    protected $description = 'Fix double order verified';

    public function handle(): void
    {
        DB::transaction(
            function () {
                try {
                    /** @var Order $order */
                    foreach ($this->getOrderQuery() as $order) {
                        foreach ($this->getPaymentByOrderQuery($order->id) as $index => $payment) {
                            if ($index === 0) continue;

                            /** @var Payment $payment */
                            $payment = Payment::findOrFail($payment->id);

                            if ($payment->status->is(PaymentStatus::PENDING)) {
                                $payment->delete();
                            }
                        }

                        foreach ($this->getShippingByOrderQuery($order->id) as $index => $shipping) {
                            if ($index === 0) continue;

                            /** @var Shipping $shipping */
                            $shipping = Shipping::findOrFail($shipping->id);

                            $shipping->delete();
                        }

                        $this->info(sprintf('Order #%s has been cleared successfully', $order->code));
                    }
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
        );
    }

    public function getOrderQuery(): LazyCollection
    {
        return DB::table('orders')
                 ->where('status', operator: OrderStatus::VERIFIED)
                 ->whereIn(
                     'id',
                     Payment::select('order_id')
                            ->whereIn('status', [PaymentStatus::PENDING, PaymentStatus::PAID])
                            ->groupBy('order_id')
                            ->having(DB::raw('count(id)'), '>', 1)
                            ->get()
                 )
                 ->cursor();
    }

    public function getPaymentByOrderQuery(string $orderId): LazyCollection
    {
        return DB::table('payments')
                 ->where('order_id', operator: $orderId)
                 ->orderBy('created_at')
                 ->cursor();
    }

    public function getShippingByOrderQuery(string $orderId): LazyCollection
    {
        return DB::table('shippings')
                 ->where('order_id', operator: $orderId)
                 ->orderBy('created_at')
                 ->cursor();
    }
}
