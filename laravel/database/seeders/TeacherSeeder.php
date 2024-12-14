<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeacherSeeder extends Seeder
{
    public function run()
    {
        DB::table('teachers')->insert([
            [
                'user_id' => 2, // teacher1 user_id
                'nip' => 'T001',
                'spesialisasi' => 'Matematika',
                'telepon' => '08123456789',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
