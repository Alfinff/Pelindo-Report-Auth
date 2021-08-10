<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Profile;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jumlahSpa      = 5;
        $jumlahSpv      = 5;
        $jumlahEos      = 10;

        // buat superadmin
        for ($i = 1; $i <= $jumlahSpa; $i++) {
            $userSpa = User::create([
                'nama' => 'superadmin' . $i,
                'email'   => 'superadmin' . $i . '@gmail.com',
                'password' => Hash::make('superadmin' . $i),
                'no_hp'   => '08' . str_shuffle('1234567890'),
                'role'     => env('ROLE_SPA'),
                'uuid'     => generateUuid(),
            ]);

            // Profile SUPERADMIN
            $dataSuperadmin = Profile::create([
                'alamat'  => 'Jl. Mantrijeron',
                'foto'    => 'foto.jpg',
                'user_id' => $userSpa->uuid,
                'uuid'    => generateUuid(),
            ]);
        }

        // buat supervisor
        for ($i = 1; $i <= $jumlahSpv; $i++) {
            $userSpv = User::create([
                'nama'     => 'Supervisor ' . $i,
                'email'    => 'supervisor' . $i . '@gmail.com',
                'password' => Hash::make('supervisor' . $i),
                'no_hp'    => '08' . str_shuffle('1234567890'),
                'role'     => env('ROLE_SPV'),
                'uuid'     => generateUuid(),
            ]);

            // Profile Super Visor
            $dataSuperVisor = Profile::create([
                'alamat'   => 'Jl. Mantrijeron',
                'foto'     => 'foto.jpg',
                'user_id'  => $userSpv->uuid,
                'uuid'     => generateUuid(),
            ]);
        }

        // buat eos
        for ($i = 1; $i <= $jumlahEos; $i++) {
            $userEos = User::create([
                'nama'     => 'Engineer On Site ' . $i,
                'email'    => 'eos' . $i . '@gmail.com',
                'password' => Hash::make('123456789'),
                'no_hp'    => '08' . str_shuffle('1234567890'),
                'role'     => env('ROLE_EOS'),
                'uuid'     => generateUuid(),
            ]);

            // Profile EOS
            $dataEODS = Profile::create([
                'uuid'     => generateUuid(),
                'user_id'  => $userEos->uuid,
                'alamat'   => 'Jl. Joko Tingkir',
                'foto'     => 'foto.jpg',
            ]);
        }

    }
}
