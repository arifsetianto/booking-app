<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use App\ValueObject\UserStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class CreateAdminUser extends Command
{
    protected $signature = 'user:create-admin';

    protected $description = 'Create admin user';

    public function handle(): void
    {
        $name = $this->ask('What is your name?');
        $email = $this->ask('What is your email?');
        $password = $this->secret('What is the password?');

        if ($this->confirm('Do you wish to continue?', true)) {
            /** @var User $user */
            $user = User::create([
                'name'              => $name,
                'email'             => $email,
                'password'          => Hash::make($password),
                'remember_token'    => Str::random(10),
                'status'            => UserStatus::COMPLETED,
            ]);
            $user->roles()->attach(Role::where('name', 'admin')->first());
            $user->markEmailAsVerified();

            $this->info('The command was successful!');
        }
    }
}
