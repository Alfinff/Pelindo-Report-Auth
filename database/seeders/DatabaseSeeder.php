<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            JKSeeder::class,
            RoleSeeder::class,
            UsersSeeder::class,
        ]);
    }
}
