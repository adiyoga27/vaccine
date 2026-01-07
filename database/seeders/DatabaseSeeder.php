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
            'BCG' => 0, 'POLIO 1' => 0, 'DPT - HIB 1' => 2, 'POLIO 2' => 2, 
            'PCV 1' => 2, 'Rotarix 1' => 2, 'DPT -HIB 2' => 3, 'POLIO 3' => 3, 
            'PCV 2' => 3, 'Rotarix 2' => 3, 'DPT -HIB 3' => 4, 'POLIO 4' => 4, 
            'I P V' => 4, 'CAMPAK - RUBELA 1' => 9, 'PCV 3' => 12, 
            'DPT -HIB Lnjt' => 18, 'CAMPAK - RUBELA 2' => 18
        ];

        foreach ($vaccines as $name => $age) {
            Vaccine::create(['name' => $name, 'minimum_age' => $age]);
        }

        // 3. Admin
        User::create([
            'name' => 'Admin Posyandu',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // 4. Dummy Users
        User::factory(10)->create(['role' => 'user'])->each(function ($user) {
            $user->patient()->create([
                'name' => fake()->name(),
                'mother_name' => fake()->name('female'),
                'date_birth' => fake()->date(),
                'address' => fake()->address(),
                'gender' => fake()->randomElement(['male', 'female']),
                'phone' => fake()->phoneNumber(),
            ]);
        });
    }
}
