<?php

use App\Models\ResourceServer;
use Illuminate\Database\Seeder;

class ResourceServerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ResourceServer::create(
            [
                'name' => 'API Hub',
                'description' => 'Banglalink API Hub',
            ]);
        ResourceServer::create(
            [
                'name' => 'Assetlite',
                'description' => 'Banglalink Assetlite backend',
            ]);
        ResourceServer::create(
            [
                'name' => 'MyBL',
                'description' => 'Banglalink My Banglalink App',
            ]);

    }
}
