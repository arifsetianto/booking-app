<?php

declare(strict_types=1);

namespace App\Listener\Auth;

use App\Event\Auth\NewMemberRegistered;
use App\Mail\Auth\NewMemberMail;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class SendNewMemberNotification implements ShouldQueue, ShouldHandleEventsAfterCommit
{
    public string $queue = 'user';

    public function handle(NewMemberRegistered $event): void
    {
        Mail::to(users: $event->user->email)->send(mailable: new NewMemberMail());
    }

    public function tags(): array
    {
        return ['listener:' . static::class, 'new-member:notification'];
    }
}
