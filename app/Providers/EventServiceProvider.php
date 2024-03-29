<?php

namespace App\Providers;

use App\Event\Auth\UserLoginRequested;
use App\Event\Order\OrderCanceled;
use App\Event\Order\OrderCompleted;
use App\Event\Order\OrderConfirmed;
use App\Event\Order\OrderForceCanceled;
use App\Event\Order\OrderInvitationConfirmed;
use App\Event\Order\OrderInvited;
use App\Event\Order\OrderPurchased;
use App\Event\Order\OrderRejected;
use App\Event\Order\OrderRevised;
use App\Event\Order\OrderVerified;
use App\Listener\Auth\SendLoginLinkVerification;
use App\Listener\Batch\AddStock;
use App\Listener\Batch\SubtractStock;
use App\Listener\Order\SendOrderCompletedNotification;
use App\Listener\Order\SendOrderConfirmedNotification;
use App\Listener\Order\SendOrderForceCanceledNotification;
use App\Listener\Order\SendOrderInvitationConfirmedNotification;
use App\Listener\Order\SendOrderInvitedNotification;
use App\Listener\Order\SendOrderPurchasedNotification;
use App\Listener\Order\SendOrderRejectedNotification;
use App\Listener\Order\SendOrderRevisedNotification;
use App\Listener\Order\SendOrderVerifiedNotification;
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
            SendOrderPurchasedNotification::class,
        ],
        OrderCanceled::class => [
            AddStock::class,
        ],
        OrderRejected::class => [
            AddStock::class,
            SendOrderRejectedNotification::class,
        ],
        OrderConfirmed::class => [
            SendOrderConfirmedNotification::class,
        ],
        OrderVerified::class => [
            SendOrderVerifiedNotification::class,
        ],
        OrderCompleted::class => [
            SendOrderCompletedNotification::class,
        ],
        OrderRevised::class => [
            SendOrderRevisedNotification::class,
        ],
        OrderForceCanceled::class => [
            AddStock::class,
            SendOrderForceCanceledNotification::class,
        ],
        OrderInvited::class => [
            SendOrderInvitedNotification::class,
        ],
        OrderInvitationConfirmed::class => [
            SubtractStock::class,
            SendOrderInvitationConfirmedNotification::class,
        ],
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
