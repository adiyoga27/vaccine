<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vaccine;
use App\Models\Village;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Villages
        // 1. Villages and their Posyandus
        $villages = [
            'Gubuk Baru' => ['Mawar', 'Cempaka', 'Lestari'],
            'SBJ Barat' => ['Melati', 'Kenanga', 'Sejahtera'],
            'SBJ Perigi' => ['Anggrek', 'Dahlia', 'Bahagia'],
            'Dompo Indah' => ['Teratai', 'Flamboyan', 'Indah'],
            'Sangiang' => ['Kamboja', 'Alamanda', 'Sehat'],
            'SBJ Timur' => ['Bougenville', 'Asoka', 'Makmur'],
            'Tangga' => ['Sepatu', 'Matahari', 'Ceria'],
            'Lokok Mandi' => ['Sedap Malam', 'Nusa Indah', 'Harmoni'],
            'Panggung Timur' => ['Bakung', 'Seruni', 'Sentosa'],
            'Panggung Barat' => ['Krisan', 'Amarilis', 'Damai'],
            'Lembah Berora' => ['Seroja', 'Lily', 'Abadi'],
            'Selengen' => ['Edelweis', 'Lavender', 'Jaya'],
            'Tampes' => ['Sakura', 'Jasmine', 'Mulia']
        ];

        foreach ($villages as $villageName => $posyandus) {
            $village = Village::create(['name' => $villageName]);
            
            foreach ($posyandus as $p) {
                \App\Models\Posyandu::create([
                    'village_id' => $village->id,
                    'name' => 'Posyandu ' . $p
                ]);
            }
        }

        // 2. Vaccines
        $vaccines = [
            'Hepatitis B 0' => 0,
            
            'DPT/Hib 1' => 2,
            'Polio 1' => 2,
            'PCV 1' => 2,
            'Rotavirus 1' => 2,
            
            'DPT 2' => 3,
            'Polio 2' => 3,
            'Hib 2' => 3,
            'PCV 2' => 3,
            
            'DPT 3' => 4,
            'Polio 3' => 4,
            'Hib 3' => 4,
            'Rotavirus 3' => 4, // "Rotavirus 3" inferred from 4 month list
            
            'Influenza 1' => 6,
            'Hepatitis B 3' => 6,
            
            'Campak/MR 1' => 9,
            
            'Varisela' => 12,
            'Hepatitis A' => 12,
            'Influenza 2' => 12,
            
            'Booster DPT' => 24,
            'Booster Polio' => 24,
            'Booster Influenza' => 24,
            'MR 2' => 24
        ];

        foreach ($vaccines as $name => $age) {
            Vaccine::create(['name' => $name, 'minimum_age' => $age]);
        }

        // 3. Admin
        // 3. Admin
        User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin Posyandu',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        $this->call(NotificationTemplateSeeder::class);

        // 4. Dummy Users
        // 4. Dummy Users
        $faker = \Faker\Factory::create('id_ID');

        for ($i = 0; $i <= 30; $i++) {
            $user = User::create([
                'name' => $faker->name(),
                'email' => $faker->unique()->freeEmail(),
                'password' => Hash::make('password'),
                'role' => 'user',
            ]);

            $user->patient()->create([
                'name' => $faker->name(),
                'mother_name' => $faker->name('female'),
                'date_birth' => now()->subMonths($i),
                'address' => $faker->address(),
                'village_id' => \App\Models\Village::inRandomOrder()->first()->id,
                'gender' => $faker->randomElement(['male', 'female']),
                'phone' => "085792486889",
            ]);
        }

        // 5. Schedules (Create schedule for each village 2 weeks from now)
        $allVaccines = Vaccine::all();
        $targetDate = now()->addWeeks(2);

        foreach (Village::all() as $village) {
            $schedule = \App\Models\VaccineSchedule::create([
                'village_id' => $village->id,
                'scheduled_at' => $targetDate,
            ]);

            // Attach all vaccines to this schedule
            $schedule->vaccines()->attach($allVaccines->pluck('id'));
        }
    }
}
