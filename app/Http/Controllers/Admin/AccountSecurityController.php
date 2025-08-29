<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Auth\SessionGuard;
use Inertia\Inertia;

class AccountSecurityController extends Controller
{
    use AuthorizesRequests;

    private function guard(): SessionGuard
    {
        /** @var SessionGuard $g */
        $g = Auth::guard('web');
        return $g;
    }

    public function index(Request $request)
    {
        $user = $request->user();

        // Sessions actives (driver 'database' requis)
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
            'current_password' => ['required', 'current_password:web'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();
        $user->forceFill(['password' => Hash::make($request->password)])->save();

        // Déconnecte les autres sessions (guard web)
        $this->guard()->logoutOtherDevices($request->password);

        // IMPORTANT : maintenir la session courante valide pour AuthenticateSession
        $request->session()->put('password_hash', $user->getAuthPassword());

        // (optionnel) révoquer les tokens Sanctum API
        if (method_exists($user, 'tokens')) {
            $user->tokens()->delete();
        }

        // Nouveau token CSRF pour éviter 419 sur le POST suivant
        $request->session()->regenerateToken();

        return back()->with('success', 'Mot de passe mis à jour et autres sessions déconnectées.');
    }

    public function updateEmail(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'current_password' => ['required', 'current_password:web'],
            'email'            => [
                'required', 'string', 'lowercase', 'email:rfc',
                'max:255', Rule::unique('users','email')->ignore($user->id),
            ],
        ]);

        if ($request->email === $user->email) {
            return back()->with('info', 'Cet email est déjà associé à votre compte.');
        }

        $user->forceFill([
            'email' => $request->email,
            'email_verified_at' => null,
        ])->save();

        if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail) {
            $user->sendEmailVerificationNotification();
        }

        $request->session()->regenerateToken();

        return back()->with('success', 'Email mis à jour.');
    }

    public function revokeSession(Request $request, string $id)
    {
        $request->validate(['id' => ['nullable']]);

        if ($id === $request->session()->getId()) {
            return back()->with('error', 'Impossible de révoquer la session actuelle.');
        }

        DB::table('sessions')->where('id', $id)->delete();

        return back()->with('success', 'Session révoquée.');
    }

    public function logoutOthers(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password:web'],
        ]);

        $this->guard()->logoutOtherDevices($request->password);

        DB::table('sessions')
            ->where('user_id', $request->user()->id)
            ->where('id', '!=', $request->session()->getId())
            ->delete();

        if (method_exists($request->user(), 'tokens')) {
            $request->user()->tokens()->delete();
        }

        $request->session()->regenerateToken();

        return back()->with('success', 'Toutes les autres sessions ont été déconnectées.');
    }
}
