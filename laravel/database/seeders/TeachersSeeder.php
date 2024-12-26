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
        // Array of real usernames with spaces
        $realUsernames = [
            'Andi Pratama',
            'Budi Santoso',
            'Citra Rahmawati',
            'Dani Purnama',
            'Elisabeth Sari',
            'Fahmi Hidayat',
            'Gita Wulandari',
            'Hendra Setiawan',
            'Indra Yulianto',
            'Julia Melati'
        ];

        foreach ($realUsernames as $i => $username) {
            // Buat data user untuk teacher dengan username yang sesuai
            $userId = DB::table('users')->insertGetId([
                'username' => $username,
                'email' => strtolower(str_replace(' ', '.', $username)) . '@example.com', // Using username with space converted to email
                'password' => Hash::make('password'), // Password default
                'role' => 'teacher',
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Buat data teacher dengan user_id yang terhubung
            DB::table('teachers')->insert([
                'user_id' => $userId,
                'nip' => 'T' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'subject_id' => rand(1, 3), // Assumes subject IDs are 1 to 3
                'telepon' => '081234567' . str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
