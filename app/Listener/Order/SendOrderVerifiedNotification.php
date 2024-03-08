<?php

declare(strict_types=1);

namespace App\Listener\Order;

use App\Event\Order\OrderVerified;
use App\Mail\Order\OrderVerifiedMail;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class SendOrderVerifiedNotification implements ShouldQueue, ShouldHandleEventsAfterCommit
{
    public string $queue = 'order';

    public function handle(OrderVerified $event): void
    {
        Mail::to(
            users: $event->getOrder()->user->email,
        )->send(
            mailable: new OrderVerifiedMail(
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
