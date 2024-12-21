<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UsersSeeder::class,
            SubjectSeeder::class,
            TeachersSeeder::class,
            StudentSeeder::class,
            ClassModelSeeder::class,
            ClassScheduleSeeder::class,
        ]);
    }
}
