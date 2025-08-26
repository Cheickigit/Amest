<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class StoreAuthLog
{
    public function handle($event): void
    {
        $ip  = Request::ip();
        $ua  = Request::header('User-Agent');
        $now = now();

        if ($event instanceof Login) {
            // log
            DB::table('auth_logs')->insert([
                'user_id'    => $event->user?->id,
                'email'      => $event->user?->email,
                'event'      => 'login_success',
                'ip_address' => $ip,
                'user_agent' => $ua,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            // maj user
            $event->user->forceFill([
                'last_login_at' => $now,
                'last_login_ip' => $ip,
            ])->saveQuietly();
        }

        if ($event instanceof Failed) {
            DB::table('auth_logs')->insert([
                'user_id'    => null,
                'email'      => $event->credentials['email'] ?? null,
                'event'      => 'login_failed',
                'ip_address' => $ip,
                'user_agent' => $ua,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if ($event instanceof Logout) {
            DB::table('auth_logs')->insert([
                'user_id'    => $event->user?->id,
                'email'      => $event->user?->email,
                'event'      => 'logout',
                'ip_address' => $ip,
                'user_agent' => $ua,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
