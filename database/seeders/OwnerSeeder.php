<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OwnerSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('OWNER_EMAIL', 'themaniak.owner@gmail.com');
        $password = env('OWNER_PASSWORD', 'ManiakOwner#2026');
        $name = env('OWNER_NAME', 'Site Owner');

        User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'role' => User::ROLE_OWNER,
                'email_verified_at' => now(),
            ]
        );
    }
}
