<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run()
    {
        $realUsernames = [
            'ahmad_fauzi', 'dian_saputra', 'nurul_hidayah', 'fitri_ayu', 'eka_prasetya',
            'agus_susilo', 'budi_hartono', 'citra_damayanti', 'desi_anugrah', 'edo_wijaya',
            'fajar_rizki', 'gita_maharani', 'hadi_kurniawan', 'indah_putri', 'joko_santoso',
            'kiki_anggraini', 'lia_wahyuni', 'maria_ulfah', 'nanda_pratama', 'oki_septian',
            'putri_amanda', 'qori_ramadhani', 'reza_maulana', 'santi_puspita', 'tono_subagyo',
            'utami_wardhani', 'vivi_nuraini', 'wawan_suryana', 'yuni_asih', 'zaki_ramli',
            'anita_susanti', 'bambang_sudarmono', 'cahya_putra', 'dewi_anggraini', 'erna_yuliana',
            'faisal_akbar', 'gina_amalia', 'haniyati_sukma', 'irma_fauziah', 'joni_susanto',
            'kartika_dewi', 'lina_kusuma', 'maya_pratiwi', 'nita_maharani', 'okky_rahmawati',
            'prima_sukma', 'qiana_hartati', 'rini_handayani', 'susan_murni', 'tri_wahyudi',
            'udin_jatmiko', 'vera_saputri', 'wira_pratama', 'yana_setiawan', 'zaenal_abidin',
            'bayu_saputra', 'chandra_sasongko', 'desi_widyaningrum', 'eka_yuliana', 'fitri_susanti',
            'gilang_prasetyo', 'haniyah_munandar', 'ida_rahayu', 'junaidi_kurniawan', 'khusnul_hidayah',
            'luthfi_akbar', 'mila_purnama', 'nata_wibawa', 'opik_ridwan', 'putra_dananjaya',
            'qodri_syahputra', 'rani_maulida', 'siti_fatimah', 'tina_wijayanti', 'udin_sudrajat',
            'vina_maulani', 'wulan_prameswari', 'yuniarti_hapsari', 'zainul_abidin', 'akbar_setiawan',
            'bambang_prasetyo', 'citra_purnamasari', 'denny_saputra', 'endi_susilo', 'farah_ananda',
            'gani_putrawan', 'hesti_rahmawati', 'irma_damayanti', 'jati_kusuma', 'karina_pratiwi',
            'lukman_hakim', 'mitha_ramadhani', 'nina_kurniasih', 'oscar_wijaya', 'pandu_pranata',
            'qarina_melia', 'rahmat_purnama', 'siti_muslimah', 'triyono_kartika', 'usman_syafii',
            'vina_rahmadani', 'widi_yulianto', 'yana_prayoga', 'zulkifli_syah', 'ade_rizky',
            'bunga_ranita', 'citra_amelia', 'dedi_suhendra', 'erni_susilawati', 'fahmi_pratama',
            'gilang_putra', 'hana_syafira', 'indra_purnama', 'junaidi_ramli', 'kiki_hartono',
            'lia_puspitasari', 'mira_pranita', 'nata_syafii', 'okta_ananda', 'prima_anggraini',
            'qory_maulana', 'rizka_putri', 'sandi_ramadani', 'tia_kusnadi', 'umar_prasetyo',
            'vina_dian', 'widi_murniati', 'yudha_prasetyo', 'zaki_maulana', 'ade_munir',
            'bimo_setiawan', 'citra_wulandari', 'dedi_kusnadi', 'eni_sukmawati', 'firman_pranata',
            'gina_kartika', 'hesti_puspita', 'ihsan_ramli', 'joni_hidayat', 'kiki_pratama',
            'linda_susilawati', 'maya_nuraini', 'nanda_kusuma', 'opri_setiawan', 'qorin_pratama',
            'reza_pradipta', 'santi_ramadhani', 'tia_hartati', 'udin_setiawan', 'vivi_damayanti',
            'wulan_hartini', 'yana_wijayanti', 'zulkarnaen_syahputra', 'andra_maulana', 'budi_prayoga',
            'cici_andriani', 'dian_purnama', 'esti_ramdani', 'ferdi_hartanto', 'gilang_ramadhan',
            'haniyah_rahmawati', 'idris_pratama', 'karina_salsabila', 'lani_dwi', 'mira_anggraeni',
            'nisa_amalia', 'oscar_kartiko', 'putri_ramdani', 'qadri_hidayat', 'rina_setiawati',
            'siti_nuraini', 'titin_ramli', 'udin_prasetyo', 'vivi_ananda', 'wawan_pranata'
        ];

        // Insert 180 students
        for ($i = 0; $i < 180; $i++) {
            $username = $realUsernames[$i % count($realUsernames)] . ($i >= count($realUsernames) ? '_' . ($i + 1) : '');
            $userId = DB::table('users')->insertGetId([
                'username' => $username,
                'email' => strtolower(str_replace(' ', '.', $username)) . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'student',
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            DB::table('students')->insert([
                'user_id' => $userId,
                'nis' => 'S' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'tanggal_lahir' => Carbon::now()->subYears(rand(15, 18))->format('Y-m-d'),
                'alamat' => 'Jl. Siswa No. ' . ($i + 1),
                'gender' => ($i % 2 === 0) ? 'male' : 'female',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
