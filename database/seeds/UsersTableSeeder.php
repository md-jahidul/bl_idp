<?php
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Creating default admin
        $user = [
            'name' => "Admin",
            'username' => 'admin@admin.com',
            'email' => "admin@admin.com",
            'mobile' => "01000000000",
            'password' => Hash::make('12345678'),
            'status' => 1,
            'user_type' => 'ADMIN',
        ];

        $user = User::create($user);
        $user->assignRole([1]); // Giving admin role
    }
}
