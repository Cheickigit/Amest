<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class AccountSecurityController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $user = $request->user();

        // sessions actives (driver database requis)
        $sessions = DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderByDesc('last_activity')
            ->get(['id', 'ip_address', 'user_agent', 'last_activity'])
            ->map(function ($s) use ($request) {
                return [
                    'id'            => $s->id,
                    'ip'            => $s->ip_address,
                    'agent'         => (string) $s->user_agent,
                    'last_activity' => \Carbon\Carbon::createFromTimestamp($s->last_activity)->toDateTimeString(),
                    'is_current'    => $s->id === $request->session()->getId(),
                ];
            })->values();

        // logs récents
        $logs = DB::table('auth_logs')
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->limit(20)
            ->get(['event','ip_address','user_agent','created_at']);

        return Inertia::render('Admin/Account/Security', [
            'user'     => [
                'name'          => $user->name,
                'email'         => $user->email,
                'roles'         => method_exists($user, 'roles') ? $user->roles->pluck('name') : [],
                'permissions'   => method_exists($user, 'permissions') ? $user->permissions->pluck('name') : [],
                'last_login_at' => $user->last_login_at?->toDateTimeString(),
                'last_login_ip' => $user->last_login_ip,
            ],
            'sessions' => $sessions,
            'logs'     => $logs,
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();
        $user->forceFill(['password' => Hash::make($request->password)])->save();

        // Déconnecte les autres sessions
        Auth::logoutOtherDevices($request->password);

        return back()->with('success', 'Mot de passe mis à jour et autres sessions déconnectées.');
    }

    public function revokeSession(Request $request, string $id)
    {
        $request->validate(['id' => ['nullable']]);
        // protège la session courante
        if ($id === $request->session()->getId()) {
            return back()->with('error', 'Impossible de révoquer la session actuelle.');
        }
        DB::table('sessions')->where('id', $id)->delete();
        return back()->with('success', 'Session révoquée.');
    }

    public function logoutOthers(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);
        Auth::logoutOtherDevices($request->password);
        // nettoie les sessions DB (sauf courante)
        DB::table('sessions')
            ->where('user_id', $request->user()->id)
            ->where('id', '!=', $request->session()->getId())
            ->delete();

        return back()->with('success', 'Toutes les autres sessions ont été déconnectées.');
    }
}
