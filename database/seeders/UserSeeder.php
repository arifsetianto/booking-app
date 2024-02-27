<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\User::factory(1)->create();
    }
}
