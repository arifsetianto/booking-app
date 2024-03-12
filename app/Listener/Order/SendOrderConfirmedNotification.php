<?php

declare(strict_types=1);

namespace App\Listener\Order;

use App\Event\Order\OrderConfirmed;
use App\Mail\Order\OrderConfirmedMail;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class SendOrderConfirmedNotification implements ShouldQueue, ShouldHandleEventsAfterCommit
{
    public string $queue = 'order';

    public function handle(OrderConfirmed $event): void
    {
        Mail::to(
            users: $event->getOrder()->user->email,
        )->send(
            mailable: new OrderConfirmedMail(
                url: URL::route(
                    name: 'orders.tracking.status',
                    parameters: [
                        'order' => $event->getOrder()->id,
                    ],
                ),
                order: $event->getOrder()
            )
        );
    }
}