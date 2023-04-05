<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\PaymentRefund;
use App\Listeners\PaymentRefundProcessed;
use Illuminate\Support\Facades\Event;
use App\Events\InstantPushNotification;
use App\Listeners\SendInstantPushNotification;
use App\Events\InstantMailNotification;
use App\Listeners\SendInstantMailNotification;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        InstantPushNotification::class => [
            SendInstantPushNotification::class
        ],
        InstantMailNotification::class => [
            SendInstantMailNotification::class
        ],
        PaymentRefund::class => [
            PaymentRefundProcessed::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
