<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassScheduleSeeder extends Seeder
{
    public function run()
    {
        // Define the classes, days, and lesson hours
        $classes = [1, 2, 3, 4]; // Example class IDs
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $lessonHours = range(1, 9); // Lesson hours 1-9

        // Track assigned schedules to prevent conflicts
        $assignedSchedules = [];

        // Loop through each class, day, and lesson hour
        foreach ($classes as $classId) {
            foreach ($days as $day) {
                foreach ($lessonHours as $lessonHour) {
                    $teacherId = rand(1, 10); // Random teacher ID between 1 and 10

                    // Ensure the teacher is not already assigned to another class at the same lesson hour on the same day
                    while (isset($assignedSchedules["$day-$lessonHour"]) && in_array($teacherId, $assignedSchedules["$day-$lessonHour"])) {
                        $teacherId = rand(1, 10); // Reassign teacher ID
                    }

                    // Assign the schedule
                    DB::table('class_schedules')->insert([
                        'class_id' => $classId,
                        'subject_id' => rand(1, 16), // Random subject ID between 1 and 16
                        'teacher_id' => $teacherId,
                        'day' => $day,
                        'lesson_hours' => $lessonHour,
                        'duration' => rand(1, 2), // Random duration for simplicity, you can adjust this logic
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Track the assigned schedule
                    $assignedSchedules["$day-$lessonHour"][] = $teacherId;
                }
            }
        }
    }
}
