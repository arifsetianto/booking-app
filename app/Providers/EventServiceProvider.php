<?php

namespace App\Providers;

use App\Event\Auth\UserLoginRequested;
use App\Event\Order\OrderCanceled;
use App\Event\Order\OrderPurchased;
use App\Event\Order\OrderRejected;
use App\Listener\Auth\SendLoginLinkVerification;
use App\Listener\Batch\AddStock;
use App\Listener\Batch\SubtractStock;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        UserLoginRequested::class => [
            SendLoginLinkVerification::class,
        ],
        OrderPurchased::class => [
            SubtractStock::class,
        ],
        OrderCanceled::class => [
            AddStock::class,
        ],
        OrderRejected::class => [
            AddStock::class,
        ]
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
