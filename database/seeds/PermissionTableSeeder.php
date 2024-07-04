<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Permission::create(['name' => 'client-management']);
        Permission::create(['name' => 'customer-management']);
        Permission::create(['name' => 'client-self-management']);
        Permission::create(['name' => 'customer-self-management']);
    }
}
