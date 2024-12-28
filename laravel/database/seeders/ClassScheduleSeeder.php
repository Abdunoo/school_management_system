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
                foreach ($lessonHours as $lessonHour) {
                    $subjectId = rand(1, 16);

                    // Find a teacher with the same subject_id
                    $teacher = DB::table('teachers')
                        ->join('subject_teacher', 'teachers.id', '=', 'subject_teacher.teacher_id')
                        ->where('subject_teacher.subject_id', $subjectId)
                        ->whereNotIn('teachers.id', $assignedSchedules["$day-$lessonHour"] ?? [])
                        ->inRandomOrder()
                        ->select('teachers.id')
                        ->first();

                    if (!$teacher) {
                        continue; // Skip if no teacher found with the subject_id
                    }

                    $teacherId = $teacher->id;

                    // Debugging information
                    echo "Class ID: $classId, Subject ID: $subjectId, Teacher ID: $teacherId, Day: $day, Lesson Hour: $lessonHour\n";

                    DB::table('class_schedules')->insert([
                        'class_id' => $classId,
                        'subject_id' => $subjectId,
                        'teacher_id' => $teacherId,
                        'day' => $day,
                        'lesson_hours' => $lessonHour,
                        'duration' => rand(1, 2),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $assignedSchedules["$day-$lessonHour"][] = $teacherId;
                }
            }
        }
    }
}
