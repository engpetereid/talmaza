<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'أبونا انطون فرج',
            'phone' => '01279530202',
            'password' => Hash::make('pass1111'),
            'role' => 'admin',
        ]);
        User::create([
            'name' => 'م\ مينا وجدى',
            'phone' => '01225498535',
            'password' => Hash::make('mina1234'),
            'role' => 'admin',
        ]);
        User::create([
            'name' => 'مساعد الامين',
            'phone' => '01222222222',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);
        $this->call(TalmazaSeeder::class);
    }
}
