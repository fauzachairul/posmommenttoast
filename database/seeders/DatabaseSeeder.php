<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Dani Permana',
        //     'email' => 'mommenttoast@gmail.com',
        //     'password' => 'mommenttoast@gmail.com',
        // ]);
        User::create([
            'name' => 'Dandi Permana',
            'email' => 'mommenttoast@gmail.com',
            'password' => Hash::make('password'),
        ]);
    }
}
