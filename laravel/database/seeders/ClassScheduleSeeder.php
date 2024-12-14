<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassScheduleSeeder extends Seeder
{
    public function run()
    {
        DB::table('class_schedules')->insert([
            [
                'class_id' => 1, // Kelas 7A
                'subject_id' => 1, // Matematika
                'teacher_id' => 1, // Teacher ID
                'day' => 'Senin',
                'lesson_hours' => 2,
                'duration' => 3, // berapa jam pelajaran
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'class_id' => 1,
                'subject_id' => 2, // Bahasa Indonesia
                'teacher_id' => 1,
                'day' => 'Selasa',
                'lesson_hours' => 2,
                'duration' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
