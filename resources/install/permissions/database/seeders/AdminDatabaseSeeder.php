<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class AdminDatabaseSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'viewRole', 'createRole', 'editRole', 'deleteRole',
            'viewPermission', 'createPermission', 'editPermission', 'deletePermission',
            'viewUser', 'createUser', 'editUser', 'deleteUser',
            'viewFlights', 'createFlights', 'editFlights', 'deleteFlights',
            'viewRegistrations', 'createRegistrations', 'editRegistrations', 'deleteRegistrations',
            'viewAirline', 'createAirline', 'editAirline', 'deleteAirline',
            'viewSchedule', 'createSchedule', 'editSchedule', 'deleteSchedule',
         ];
         foreach ($permissions as $permission) {
              Permission::create(['name' => $permission]);
         }

         // create roles and assign created permissions
        $role1 = Role::create(['name' => 'user'])->givePermissionTo(['viewFlights', 'viewRegistrations', 'viewAirline']);
        $role2 = Role::create(['name' => 'admin'])->givePermissionTo(['viewSchedule', 'createSchedule', 'viewAirline', 'createAirline','viewRegistrations', 'createRegistrations']);
        $role3 = Role::create(['name' => 'super-admin'])->givePermissionTo(Permission::all());
        
        // create demo User
        $user1 = \App\Models\User::factory()->create([ 
            'name'      => 'Site User',
            'email'     => 'user@example.com',
            'phone'     => '+2547000002',
            'title'     => 'Developer',
            'photo'     => 'image.jpg',
        ])->assignRole($role1);

        // create demo Admin
        $user2 = \App\Models\User::factory()->create([
            'name'      => 'Site Admin',
            'email'     => 'admin@example.com',
            'phone'     => '+2547000001',
            'title'     => 'Developer',
            'photo'     => 'avatar.jpg',
        ])->assignRole($role2);
        
        // create demo Super-admin
        $user3 = \App\Models\User::factory()->create([
            'name'      => 'Super Admin',
            'email'     => 'super-admin@example.com',
            'phone'     => '+2547000000',
            'title'     => 'Developer',
            'photo'     => 'image.jpg',

        ])->assignRole($role3);
    }
}