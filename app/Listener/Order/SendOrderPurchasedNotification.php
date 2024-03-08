<?php

declare(strict_types=1);

namespace App\Listener\Order;

use App\Event\Order\OrderPurchased;
use App\Mail\Order\OrderPurchasedMail;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Contracts\Queue\ShouldQueue;
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
                    name: 'orders.payment',
                    parameters: [
                        'order' => $event->getOrder()->id,
                    ],
                ),
                order: $event->getOrder()
            )
        );
    }
}
