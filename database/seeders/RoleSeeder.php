<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $SPA = Role::create([
            'code' => env('ROLE_SPA'),
            'name' => 'Super Admin',
            'uuid' => generateUuid(),
        ]);

        $SPV = Role::create([
            'code' => env('ROLE_SPV'),
            'name' => 'Super Visor',
            'uuid' => generateUuid(),
        ]);

        $EOS = Role::create([
            'code' => env('ROLE_EOS'),
            'name' => 'Engineer On Site',
            'uuid' => generateUuid(),
        ]);

    }
}
