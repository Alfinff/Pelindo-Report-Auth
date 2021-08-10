<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\JenisKelamin;

class JKSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dataJK = JenisKelamin::create([
            'nama'   => 'Laki-Laki',
            'uuid'     => generateUuid(),
        ]);

        $dataJK = JenisKelamin::create([
            'nama'   => 'Perempuan',
            'uuid'     => generateUuid(),
        ]);
    }
}
