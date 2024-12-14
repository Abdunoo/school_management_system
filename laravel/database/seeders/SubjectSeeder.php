<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    public function run()
    {
        DB::table('subjects')->insert([
            ['name' => 'Matematika', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bahasa Indonesia', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'IPA', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
