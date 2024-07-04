<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $admin = Role::create(['name' => 'admin', 'guard_name' => 'web']); // Role 1- admin
        $admin->syncPermissions([1, 2]); // 1- client-management, 2- customer-managemet 

        $client = Role::create(['name' => 'idp-client', 'guard_name' => 'web']);  // Role 2- idp-client
        $client->syncPermissions([3]); // 3- client-self-management 

        $client = Role::create(['name' => 'idp-customer', 'guard_name' => 'web']);  // Role 2- idp-client
        $client->syncPermissions([4]); // 3- customer-self-management 
    }
}
