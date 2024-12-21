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
        for ($i = 1; $i <= 20; $i++) {
            // Buat data user untuk teacher
            $userId = DB::table('users')->insertGetId([
                'username' => 'teacher' . $i,
                'email' => 'teacher' . $i . '@example.com',
                'password' => Hash::make('password'), // Password default
                'role' => 'teacher',
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Buat data teacher dengan user_id yang terhubung
            DB::table('teachers')->insert([
                'user_id' => $userId,
                'nip' => 'T' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'subject_id' => rand(1,3),
                'telepon' => '081234567' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
