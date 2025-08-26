<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),

            // Expose l'utilisateur + rôles/permissions au front (Inertia)
            'auth' => [
                'user' => $user ? [
                    'id'           => $user->id,
                    'name'         => $user->name,
                    'email'        => $user->email,
                    'roles'        => $user->getRoleNames(),                          // ex: ["admin"]
                    'permissions'  => $user->getAllPermissions()->pluck('name'),      // ex: ["projects.view", ...]
                    'created_at'   => $user->created_at,
                ] : null,
            ],

            // CSRF pratique côté front (logout formulaires, etc.)
            'csrf_token' => csrf_token(),

            // Ziggy (routes JS)
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
        ];
    }
}
