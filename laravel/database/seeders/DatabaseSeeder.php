<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UsersSeeder::class,
            TeachersSeeder::class,
            StudentSeeder::class,
            SubjectSeeder::class,
            ClassModelSeeder::class,
            ClassScheduleSeeder::class,
        ]);
    }
}
