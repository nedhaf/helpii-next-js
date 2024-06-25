<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Auth\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class HelpiiAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'backend']);

        $role = Role::create(['name' => config('access.users.admin_role')]);
        $role->givePermissionTo('backend');

        $user = Role::create(['name' => config('access.users.default_role')]);

        // Add the master administrator user
        $UserAdmin = User::create([
            'first_name'        => 'Helpii',
            'last_name'         => 'Administrator',
            'email'             => 'admin@admin.com',
            'password'          => Hash::make('Vattenfall#2'),
            'confirmation_code' => md5(uniqid(mt_rand(), true)),
            'confirmed'         => true,
            'slug'              => 'helpii-administrator'
        ]);

        $UserAdmin->assignRole($role);
    }
}
