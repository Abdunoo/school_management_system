<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            StudentSeeder::class,
            SubjectSeeder::class,
            TeachersSeeder::class,
            ClassModelSeeder::class,
            ClassScheduleSeeder::class,
        ]);
    }
}
