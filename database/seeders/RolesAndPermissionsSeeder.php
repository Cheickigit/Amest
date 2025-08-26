<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'access admin', 'access client portal',
            'projects.view','projects.create','projects.edit','projects.delete','projects.publish',
            'quotes.view','quotes.manage',
            'rfps.view','rfps.manage',
            'clients.view','clients.manage',
            'reports.view','reports.manage',
            'cms.manage',
            'users.manage',
        ];
        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        $admin  = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $chef   = Role::firstOrCreate(['name' => 'chef_projet', 'guard_name' => 'web']);
        $client = Role::firstOrCreate(['name' => 'client', 'guard_name' => 'web']);

        $admin->syncPermissions(Permission::all());
        $chef->syncPermissions([
            'access admin',
            'projects.view','projects.create','projects.edit','projects.delete','projects.publish',
            'quotes.view','quotes.manage',
            'rfps.view','rfps.manage',
            'reports.view','reports.manage',
        ]);
        $client->syncPermissions(['access client portal']);

        // --- Comptes démo (mise à jour si existent déjà) ---
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@bkconstruction.test'],
            ['name' => 'Admin BK', 'password' => Hash::make('password'), 'email_verified_at' => now()]
        );
        $adminUser->syncRoles(['admin']);

        $chefUser = User::updateOrCreate(
            ['email' => 'chef@bkconstruction.test'],
            ['name' => 'Chef de projet BK', 'password' => Hash::make('password'), 'email_verified_at' => now()]
        );
        $chefUser->syncRoles(['chef_projet']);

        $clientUser = User::updateOrCreate(
            ['email' => 'client@bkconstruction.test'],
            ['name' => 'Client Démo', 'password' => Hash::make('password'), 'email_verified_at' => now()]
        );
        $clientUser->syncRoles(['client']);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
