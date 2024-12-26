<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    public function run()
    {
        DB::table('subjects')->insert([
            ['name' => 'Bahasa Indonesia', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bahasa Inggris', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bahasa Jerman', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'IPA', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'IPS', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Matematika', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'PKN', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sejarah', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Geografi', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ekonomi', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Seni Budaya', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pendidikan Jasmani', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'TIK', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kimia', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Fisika', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Biologi', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
