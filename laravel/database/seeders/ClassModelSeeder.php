<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassModelSeeder extends Seeder
{
    public function run()
    {
        DB::table('classes')->insert([
            [
                'name' => 'Kelas 7A',
                'academic_year' => '2024',
                'homeroom_teacher_id' => 1, // teacher_id
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
