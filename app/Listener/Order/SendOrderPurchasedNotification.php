<?php

declare(strict_types=1);

namespace App\Listener\Order;

use App\Event\Order\OrderPurchased;
use App\Mail\Order\OrderPurchasedMail;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class SendOrderPurchasedNotification implements ShouldQueue, ShouldHandleEventsAfterCommit
{
    public string $queue = 'order';

    public function handle(OrderPurchased $event): void
    {
        Mail::to(
            users: $event->getOrder()->user->email,
        )->send(
            mailable: new OrderPurchasedMail(
                url: URL::route(
                    name: 'orders.payment.force',
                    parameters: [
                        'order' => $event->getOrder()->id,
                        'email' => $event->getOrder()->user->email,
                    ],
                ),
                order: $event->getOrder()
            )
        );
    }

    public function middleware(): array
    {
        return [new RateLimited('emails')];
    }
}
