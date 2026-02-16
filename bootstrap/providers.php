<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Modules\Auth\Providers\AuthModuleServiceProvider::class,
    App\Modules\Event\Providers\EventModuleServiceProvider::class,
    App\Modules\Ticket\Providers\TicketModuleServiceProvider::class,
    App\Modules\Booking\Providers\BookingModuleServiceProvider::class,
    App\Modules\Payment\Providers\PaymentModuleServiceProvider::class,
];
