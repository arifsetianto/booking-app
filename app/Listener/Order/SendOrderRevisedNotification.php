<?php

declare(strict_types=1);

namespace App\Listener\Order;

use App\Event\Order\OrderRevised;
use App\Mail\Order\OrderRevisedMail;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class SendOrderRevisedNotification implements ShouldQueue, ShouldHandleEventsAfterCommit
{
    public string $queue = 'order';

    public function handle(OrderRevised $event): void
    {
        Mail::to(
            users: $event->getOrder()->user->email,
        )->send(
            mailable: new OrderRevisedMail(
                url: URL::signedRoute(
                    name: 'orders.request-update',
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
