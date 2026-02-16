<?php

namespace App\Modules\Auth\Providers;

use App\Application\Contracts\Services\AuthServiceInterface;
use App\Application\Services\Auth\AuthService;
use Illuminate\Support\ServiceProvider;

class AuthModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
    }
}
