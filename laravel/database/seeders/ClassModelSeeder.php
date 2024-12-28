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

        // List of unique teacher IDs (assuming there are 50 teachers)
        $homeroomTeacherIds = range(1, 50);
        shuffle($homeroomTeacherIds); // Shuffle for random assignment

        $teacherIndex = 0;

        foreach ($grades as $grade) {
            foreach ($majors as $major) {
                foreach ($classNumbers as $classNumber) {
                    if ($teacherIndex >= count($homeroomTeacherIds)) {
                        throw new \Exception("Not enough unique homeroom teachers for all classes.");
                    }

                    DB::table('classes')->insert([
                        'name' => "{$grade} {$major} {$classNumber}",
                        'academic_year' => '2024/2025',
                        'homeroom_teacher_id' => $homeroomTeacherIds[$teacherIndex++], // Unique teacher ID
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
