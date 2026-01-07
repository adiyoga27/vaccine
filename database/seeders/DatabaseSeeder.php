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
        $villages = [
            'Gubuk Baru', 'SBJ Barat', 'SBJ Perigi', 'Dompo Indah', 'Sangiang', 
            'SBJ Timur', 'Tangga', 'Lokok Mandi', 'Panggung Timur', 
            'Panggung Barat', 'Lembah Berora', 'Selengen', 'Tampes'
        ];

        foreach ($villages as $v) {
            Village::create(['name' => $v]);
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

        // 4. Dummy Users
        User::factory(10)->create(['role' => 'user'])->each(function ($user) {
            $user->patient()->create([
                'name' => fake()->name(),
                'mother_name' => fake()->name('female'),
                'date_birth' => fake()->date(),
                'address' => fake()->address(),
                'village_id' => \App\Models\Village::inRandomOrder()->first()->id, // Assign random village
                'gender' => fake()->randomElement(['male', 'female']),
                'phone' => fake()->phoneNumber(),
            ]);
        });

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
