<?php

namespace Database\Seeders;

use App\Models\Office;
use Illuminate\Database\Seeder;

class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $offices = [
            [
                'name' => 'Dinas Kesehatan Kabupaten',
                'address' => 'Jl. Kesehatan No. 1, Pusat Kota',
            ],
            [
                'name' => 'Puskesmas Labuapi',
                'address' => 'Jl. Raya Labuapi, Kec. Labuapi',
            ],
            [
                'name' => 'Kantor Camat Labuapi',
                'address' => 'Jl. Protokol No. 10, Labuapi',
            ],
        ];

        foreach ($offices as $office) {
            Office::firstOrCreate(['name' => $office['name']], $office);
        }
    }
}
