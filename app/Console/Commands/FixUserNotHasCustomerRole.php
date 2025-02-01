<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class FixUserNotHasCustomerRole extends Command
{
    protected $signature = 'user:customer:fix-not-has-role';

    protected $description = 'Fix user not has customer role';

    public function handle(): void
    {
        $this->withProgressBar($this->getUserQuery(), function (User $user) {
            if (!$user->hasRole('customer')) {
                $user->roles()->attach(Role::where('name', 'customer')->first());
            }
        });
    }

    public function getUserQuery(): LazyCollection
    {
        return User::query()
                 ->whereNotIn('id', DB::table('role_user')->get()->pluck('user_id')->toArray())
                 ->cursor();
    }
}
