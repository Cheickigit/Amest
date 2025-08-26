<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Réinitialise le cache Spatie à la fin (inutile au début)
        // app(PermissionRegistrar::class)->forgetCachedPermissions();

        /* --- Permissions --- */
        $permissions = [
            'access admin',
            'access client portal',

            'projects.view','projects.create','projects.edit','projects.delete','projects.publish',

            'quotes.view','quotes.manage',
            'rfps.view','rfps.manage',

            // standardisé sur "contacts"
            'contacts.view','contacts.manage',

            'reports.view','reports.manage',
            'cms.manage',
            'users.manage',
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        /* --- Rôles --- */
        $admin  = Role::firstOrCreate(['name' => 'admin',       'guard_name' => 'web']);
        $chef   = Role::firstOrCreate(['name' => 'chef_projet', 'guard_name' => 'web']);
        $client = Role::firstOrCreate(['name' => 'client',      'guard_name' => 'web']);

        // Admin : tout
        $admin->syncPermissions(Permission::where('guard_name', 'web')->pluck('name'));

        // Chef de projet : accès admin + modules métiers courants
        $chef->syncPermissions([
            'access admin',
            'projects.view','projects.create','projects.edit','projects.delete','projects.publish',
            'quotes.view','quotes.manage',
            'rfps.view','rfps.manage',
            'contacts.view', // lecture contacts
            'reports.view','reports.manage',
        ]);

        // Client : portail client uniquement
        $client->syncPermissions(['access client portal']);

        /* --- Comptes démo --- */
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@bko.local'],
            ['name' => 'BKO Admin', 'password' => Hash::make('password'), 'email_verified_at' => now()]
        );
        $adminUser->syncRoles(['admin']);

        $chefUser = User::updateOrCreate(
            ['email' => 'chef@bko.local'],
            ['name' => 'Chef de projet BKO', 'password' => Hash::make('password'), 'email_verified_at' => now()]
        );
        $chefUser->syncRoles(['chef_projet']);

        $clientUser = User::updateOrCreate(
            ['email' => 'client@bko.local'],
            ['name' => 'Client Démo', 'password' => Hash::make('password'), 'email_verified_at' => now()]
        );
        $clientUser->syncRoles(['client']);

        // Purge cache permissions (une seule fois, ici)
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
