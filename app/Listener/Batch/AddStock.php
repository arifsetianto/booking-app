<?php

declare(strict_types=1);

namespace App\Listener\Batch;

use App\Event\Order\OrderCanceled;
use App\Event\Order\OrderRejected;
use App\Models\Batch;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class AddStock implements ShouldQueue, ShouldHandleEventsAfterCommit
{
    public string $queue = 'order';

    public function handle(OrderCanceled|OrderRejected $event): void
    {
        /** @var Batch $batch */
        $batch = Batch::findOrFail($event->getOrder()->batch->id);

        if ($batch->purchased_stock >= $event->getOrder()->qty) {
            $batch->purchased_stock -= $event->getOrder()->qty;
        }

        $batch->save();
    }

    public function middleware(OrderCanceled|OrderRejected $event): array
    {
        return [
            (new WithoutOverlapping(sprintf('batch-%s', $event->getOrder()->batch->id)))
                ->releaseAfter(5)
                ->expireAfter(60 * 15)
        ];
    }

    public function tags(): array
    {
        return ['listener:' . static::class, 'stock:add'];
    }
}
