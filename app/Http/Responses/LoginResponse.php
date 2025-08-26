<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        $target = match (true) {
            $user->hasRole('admin'), $user->hasRole('chef_projet') => route('admin.dashboard'),
            $user->hasRole('client') => route('client.portal'),
            default => route('dashboard'),
        };

        return redirect()->intended($target);
    }
}
