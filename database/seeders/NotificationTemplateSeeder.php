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
        $templates = [
            [
                'slug' => 'daily_reminder',
                'name' => 'Pengingat Harian',
                'variables' => json_encode(['parent_name', 'child_name', 'vaccine_name', 'date_birth', 'address', 'age', 'posyandu_name']),
                'content' => "Halo Bunda *[parent_name]*! 👋\n\nApa kabar? Semoga sehat selalu ya! Kami mau ngingetin nih, si kecil *[child_name]* udah waktunya buat imunisasi *[vaccine_name]* lho. 👶💉\n\nYuk, pastikan si kecil tetap sehat dan kuat dengan datang ke *[posyandu_name]* ya! Jangan sampai terlewat momen penting ini untuk masa depannya.\n\n📍 *Alamat:* [address]\n📅 *Usia si Kecil:* [age]\n\nSampai jumpa di Posyandu ya, Bunda! Semangat! 💪✨",
            ],
            [
                'slug' => 'vaccine_completed',
                'name' => 'Sertifikat Kelulusan',
                'variables' => json_encode(['parent_name', 'child_name', 'certificate_link']),
                'content' => "Yeaaay! Selamat ya Bunda *[parent_name]*! 🎉🥳\n\nHebat banget! Si kecil *[child_name]* sudah *LULUS* semua tahapan imunisasi dasar! Kami bangga banget sama perjuangan Bunda dan si kecil. 🥰\n\nSebagai tanda apresiasi, ini ada Sertifikat Kelulusan Imunisasi buat kenang-kenangan. Bisa langsung didownload di sini ya:\n👉 [certificate_link]\n\nTerima kasih sudah menjaga kesehatan si kecil bersama kami. Semoga [child_name] tumbuh jadi anak yang sehat, pinter, dan membanggakan! ❤️",
            ],
            [
                'slug' => 'vaccine_approved',
                'name' => 'Vaksin Disetujui',
                'variables' => json_encode(['parent_name', 'child_name', 'vaccine_name', 'posyandu_name']),
                'content' => "Halo Bunda *[parent_name]*! 👋\n\nTerima kasih yaa sudah membawa *[child_name]* untuk imunisasi *[vaccine_name]* di *[posyandu_name]*. Data imunisasinya sudah berhasil kami catat di sistem! ✅\n\nSehat terus ya buat si kecil, dan jangan lupa cek jadwal imunisasi selanjutnya! Kalau ada apa-apa, jangan ragu tanya kami ya. 😊\n\nSalam sayang dari Kader Posyandu! ❤️",
            ]
        ];

        foreach ($templates as $template) {
            \App\Models\NotificationTemplate::updateOrCreate(
                ['slug' => $template['slug']],
                $template
            );
        }
    }
}
