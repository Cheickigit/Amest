<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Logout;
use App\Listeners\StoreAuthLog;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Login::class  => [StoreAuthLog::class],
        Failed::class => [StoreAuthLog::class],
        Logout::class => [StoreAuthLog::class],
    ];
}
