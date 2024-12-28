<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TeachersSeeder extends Seeder
{
    public function run()
    {
        $realUsernames = [
            'Andi Pratama', 'Budi Santoso', 'Citra Rahmawati', 'Dani Purnama', 'Elisabeth Sari',
            'Fahmi Hidayat', 'Gita Wulandari', 'Hendra Setiawan', 'Indra Yulianto', 'Julia Melati',
            'Kurniawan Putra', 'Lestari Dewi', 'Maya Sari', 'Nina Puspita', 'Oki Setiawan',
            'Putu Wijaya', 'Qori Hidayat', 'Rina Wulandari', 'Sari Dewi', 'Taufik Hidayat',
            'Umar Setiawan', 'Vina Melati', 'Wahyu Pratama', 'Xena Rahmawati', 'Yusuf Purnama',
            'Zahra Sari', 'Ayu Hidayat', 'Bambang Wulandari', 'Cici Setiawan', 'Dodi Yulianto',
            'Eka Melati', 'Fajar Pratama', 'Gilang Santoso', 'Hana Rahmawati', 'Iwan Purnama',
            'Joko Sari', 'Kiki Hidayat', 'Lina Wulandari', 'Maman Setiawan', 'Novi Yulianto',
            'Omar Melati', 'Pipit Pratama', 'Qori Santoso', 'Rina Rahmawati', 'Siti Purnama',
            'Toni Sari', 'Ujang Setiawan', 'Vera Melati', 'Wulan Pratama', 'Xavier Santoso', 'Yana Rahmawati'
        ];

        foreach ($realUsernames as $i => $username) {
            $userId = DB::table('users')->insertGetId([
                'username' => $username,
                'email' => strtolower(str_replace(' ', '.', $username)) . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $teacherId = DB::table('teachers')->insertGetId([
                'user_id' => $userId,
                'nip' => 'T' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'telepon' => '081234567' . str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                'gender' => $i < 32 ? 'male' : 'female',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $subjectIds = range(1, 16);
            shuffle($subjectIds);
            $assignedSubjects = array_slice($subjectIds, 0, rand(1, 3));

            foreach ($assignedSubjects as $subjectId) {
                DB::table('subject_teacher')->insert([
                    'teacher_id' => $teacherId,
                    'subject_id' => $subjectId,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}
