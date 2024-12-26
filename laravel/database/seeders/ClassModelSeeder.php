<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassModelSeeder extends Seeder
{
    public function run()
    {
        // Define the classes for grade 10, 11, and 12 with IPA and IPS
        $grades = [10, 11, 12];
        $majors = ['IPA', 'IPS'];
        $classNumbers = [1, 2, 3, 4];

        foreach ($grades as $grade) {
            foreach ($majors as $major) {
                foreach ($classNumbers as $classNumber) {
                    DB::table('classes')->insert([
                        'name' => "{$grade} {$major} {$classNumber}",
                        'academic_year' => '2024/2025',
                        'homeroom_teacher_id' => rand(1, 10), // Assuming homeroom teacher_id is between 1 and 10
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
