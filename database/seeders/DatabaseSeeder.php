<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Admin;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->createMany([
            [
                'name' => 'User 1',
                'email' => 'user1@gmail.com',
                'password' => Hash::make('password'),
                'email_verified_at' => Carbon::now()->startOfDay(),
            ],
            [
                'name' => 'User 2',
                'email' => 'user2@gmail.com',
                'password' => Hash::make('password'),
                'email_verified_at' => Carbon::now()->startOfDay(),
            ]
        ]);

        Admin::create([
            'name' => 'Admin 1',
            'email' => 'admin1@gmail.com',
            'password' => Hash::make('password'),
        ]);
    }
}
