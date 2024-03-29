<?php

declare(strict_types=1);

namespace App\Listener\Auth;

use App\Event\Auth\UserLoginRequested;
use App\Mail\Auth\LoginLink;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class SendLoginLinkVerification implements ShouldQueue, ShouldHandleEventsAfterCommit
{
    public string $queue = 'user';

    public function handle(UserLoginRequested $event): void
    {
        Mail::to(
            users: $event->getEmail(),
        )->send(
            mailable: new LoginLink(
                url: URL::temporarySignedRoute(
                    name: 'login.email.store',
                    expiration: 3600,
                    parameters: [
                        'email' => $event->getEmail(),
                    ],
                ),
            )
        );
    }

    public function middleware(UserLoginRequested $event): array
    {
        return [
            new RateLimited('emails'),
        ];
    }

    public function tags(): array
    {
        return ['listener:' . static::class, 'login-link:send'];
    }
}
