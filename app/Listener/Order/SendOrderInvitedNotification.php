<?php

declare(strict_types=1);

namespace App\Listener\Order;

use App\Event\Order\OrderInvited;
use App\Mail\Order\OrderInvitedMail;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class SendOrderInvitedNotification implements ShouldQueue, ShouldHandleEventsAfterCommit
{
    public string $queue = 'order';

    public function handle(OrderInvited $event): void
    {
        Mail::to(
            users: $event->getOrder()->user->email,
        )->send(
            mailable: new OrderInvitedMail(
                url: URL::signedRoute(
                    name: 'orders.confirm-invitation',
                    parameters: [
                        'order' => $event->getOrder()->id,
                        'email' => $event->getOrder()->user->email,
                    ],
                ),
                order: $event->getOrder(),
                useReference: $event->isUseReference()
            )
        );
    }

    //public function middleware(OrderInvited $event): array
    //{
    //    return [
    //        new RateLimited('emails'),
    //    ];
    //}

    public function tags(): array
    {
        return ['listener:' . static::class, 'order-invited:send'];
    }
}
