<?php

declare(strict_types=1);

namespace App\Listener\Order;

use App\Event\Order\OrderInvitationConfirmed;
use App\Mail\Order\OrderConfirmedMail;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class SendOrderInvitationConfirmedNotification implements ShouldQueue, ShouldHandleEventsAfterCommit
{
    public string $queue = 'order';

    public function handle(OrderInvitationConfirmed $event): void
    {
        Mail::to(
            users: $event->getOrder()->user->email,
        )->send(
            mailable: new OrderConfirmedMail(
                url: URL::route(
                    name: 'orders.tracking.status.force',
                    parameters: [
                        'order' => $event->getOrder()->id,
                        'email' => $event->getOrder()->user->email,
                    ],
                ),
                order: $event->getOrder()
            )
        );
    }

    public function middleware(OrderInvitationConfirmed $event): array
    {
        return [
            new RateLimited('emails'),
        ];
    }

    public function tags(): array
    {
        return ['listener:' . static::class, 'order-invitation-confirmed:send'];
    }
}
