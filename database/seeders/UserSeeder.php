<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\ValueObject\UserStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        /** @var User $user */
        $user = User::create([
            'name'              => 'Administrator',
            'email'             => 'admin@thaiquran.com',
            'email_verified_at' => now(),
            'password'          => Hash::make('password'),
            'remember_token'    => Str::random(10),
            'status'            => UserStatus::COMPLETED
        ]);
        $user->roles()->attach(Role::where('name', 'admin')->first());
    }
}
