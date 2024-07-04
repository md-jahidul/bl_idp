<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PermissionTableSeeder::class); // Permissions Table
        $this->call(RoleTableSeeder::class); // Roles Table
        $this->call(UsersTableSeeder::class); // Users Table
    }
}
