<?php

namespace App\Providers;

use App\Interfaces\Repositories\TransactionRepositoryInterface;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Interfaces\Services\AuthServiceInterface;
use App\Interfaces\Services\TransactionServiceInterface;
use App\Repositories\TransactionRepository;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Services\TransactionService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->register(RouteServiceProvider::class);
        $this->app->bind(TransactionRepositoryInterface::class, TransactionRepository::class);
        $this->app->bind(TransactionServiceInterface::class, TransactionService::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
