<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassScheduleSeeder extends Seeder
{
    public function run()
    {
        $classes = [1, 2, 3, 4];
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $lessonHours = range(1, 9);

        $assignedSchedules = [];

        foreach ($classes as $classId) {
            foreach ($days as $day) {
                $usedLessonHours = []; // Track used lesson hours for this day and class

                foreach ($lessonHours as $lessonHour) {
                    // Skip if this lesson hour is already used
                    if (in_array($lessonHour, $usedLessonHours)) {
                        continue;
                    }

                    $subjectId = rand(1, 16);

                    // Find a teacher with the same subject_id
                    $teacher = DB::table('teachers')
                        ->join('subject_teacher', 'teachers.id', '=', 'subject_teacher.teacher_id')
                        ->where('subject_teacher.subject_id', $subjectId)
                        ->inRandomOrder()
                        ->select('teachers.id')
                        ->first();

                    if (!$teacher) {
                        continue; // Skip if no teacher found with the subject_id
                    }

                    $teacherId = $teacher->id;

                    // Generate a random duration (2 or 3 hours)
                    $duration = rand(2, 3);

                    // Check if the lesson hours are available
                    $conflict = false;
                    for ($i = $lessonHour; $i < $lessonHour + $duration; $i++) {
                        if (in_array($i, $usedLessonHours)) {
                            $conflict = true;
                            break;
                        }
                    }

                    if ($conflict) {
                        continue; // Skip if there's a conflict
                    }

                    // Mark the lesson hours as used
                    for ($i = $lessonHour; $i < $lessonHour + $duration; $i++) {
                        $usedLessonHours[] = $i;
                    }

                    // Insert the schedule
                    DB::table('class_schedules')->insert([
                        'class_id' => $classId,
                        'subject_id' => $subjectId,
                        'teacher_id' => $teacherId,
                        'day' => $day,
                        'lesson_hours' => $lessonHour,
                        'duration' => $duration,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Debugging information
                    echo "Class ID: $classId, Subject ID: $subjectId, Teacher ID: $teacherId, Day: $day, Lesson Hour: $lessonHour, Duration: $duration\n";
                }
            }
        }
    }
}
