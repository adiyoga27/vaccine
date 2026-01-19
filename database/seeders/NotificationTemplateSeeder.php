<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        \App\Models\NotificationTemplate::updateOrCreate(
            ['slug' => 'daily_reminder'],
            [
                'name' => 'Pengingat Harian Vaksin',
                'content' => "Halo Ibu [mother_name],\n\nMengingatkan bahwa jadwal vaksinasi [vaccine_name] untuk anak [patient_name] adalah HARI INI di Posyandu [posyandu_name] - Desa [village_name].\n\nSilakan datang tepat waktu.\nTerima kasih.",
                'variables' => 'mother_name, vaccine_name, patient_name, posyandu_name, village_name'
            ]
        );

        \App\Models\NotificationTemplate::updateOrCreate(
            ['slug' => 'vaccine_completed'],
            [
                'name' => 'Sertifikat Lulus Imunisasi',
                'content' => "Selamat! Anak [patient_name] telah menyelesaikan seluruh rangkaian vaksinasi wajib.\n\nSertifikat kelulusan imunisasi dapat diunduh melalui link berikut:\n[certificate_link]\n\nTerima kasih telah menjaga kesehatan si kecil!",
                'variables' => 'patient_name, certificate_link'
            ]
        );

        \App\Models\NotificationTemplate::updateOrCreate(
            ['slug' => 'vaccine_approved'],
            [
                'name' => 'Konfirmasi Vaksinasi Selesai',
                'content' => "Halo,\n\nInformasi bahwa anak [patient_name] TELAH SELESAI melakukan vaksinasi [vaccine_name] pada tanggal [vaccinated_at] di [posyandu_name].\n\nTerima kasih telah datang ke Posyandu.",
                'variables' => 'patient_name, vaccine_name, vaccinated_at, posyandu_name'
            ]
        );
    }
}
