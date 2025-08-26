<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

Route::get('/', fn () => Inertia::render('Welcome'))->name('home');

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {

    Route::middleware(['role:client|admin|chef_projet', 'permission:access client portal|access admin'])
        ->group(function () {
            Route::get('/client', fn () => Inertia::render('Client/Portal'))->name('client.portal');
        });

    Route::prefix('admin')
        ->middleware(['role:admin|chef_projet', 'permission:access admin'])
        ->group(function () {
            Route::get('/', fn () => Inertia::render('Admin/Dashboard'))->name('admin.dashboard');
        });

    Route::get('/dashboard', function () {
        $u = Auth::user();
        if ($u && ($u->hasRole('admin') || $u->hasRole('chef_projet'))) {
            return redirect()->route('admin.dashboard');
        }
        if ($u && $u->hasRole('client')) {
            return redirect()->route('client.portal');
        }
        return Inertia::render('Dashboard');
    })->name('dashboard');
});
