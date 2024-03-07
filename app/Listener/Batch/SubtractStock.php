<?php

declare(strict_types=1);

namespace App\Listener\Batch;

use App\Event\Order\OrderPurchased;
use App\Models\Batch;
use App\Models\Order;
use App\ValueObject\OrderStatus;
use Carbon\Carbon;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class SubtractStock implements ShouldQueue, ShouldHandleEventsAfterCommit
{
    public string $queue = 'order';

    public function handle(OrderPurchased $event): void
    {
        /** @var Batch $batch */
        $batch = Batch::findOrFail($event->getOrder()->batch->id);

        if ($batch->getAvailableStock() < 1) {
            /** @var Order $order */
            $order = Order::findOrFail($event->getOrder()->id);
            $order->canceled_at = Carbon::now();
            $order->status = OrderStatus::CANCELED;
            $order->reason = 'Stock is out, please order in the next batch.';

            $order->save();
        }

        $batch->purchased_stock += $event->getOrder()->qty;

        $batch->save();
    }

    public function middleware(OrderPurchased $event): array
    {
        return [
            (new WithoutOverlapping(sprintf('batch-%s', $event->getOrder()->batch->id)))
                ->releaseAfter(5)
                ->expireAfter(60 * 15)
        ];
    }

    public function tags(): array
    {
        return ['listener:' . static::class, 'stock:subtract'];
    }
}
