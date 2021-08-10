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
            'kode' => env('ROLE_SPA'),
            'nama' => 'Super Admin',
            'uuid' => generateUuid(),
        ]);

        $SPV = Role::create([
            'kode' => env('ROLE_SPV'),
            'nama' => 'Super Visor',
            'uuid' => generateUuid(),
        ]);

        $EOS = Role::create([
            'kode' => env('ROLE_EOS'),
            'nama' => 'Engineer On Site',
            'uuid' => generateUuid(),
        ]);

    }
}
