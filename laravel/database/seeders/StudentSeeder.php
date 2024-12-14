<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentSeeder extends Seeder
{
    public function run()
    {
        DB::table('students')->insert([
            [
                'user_id' => 3, // student1 user_id
                'nis' => 'S001',
                'tanggal_lahir' => '2010-01-01',
                'alamat' => 'Jl. Merdeka No. 1',
                'gender' => 'male',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
