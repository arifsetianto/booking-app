<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class RoleTableSeeder extends Seeder
{
    const DATA = [
        [
            'id'    => '8d0bfd6c-813d-446c-ac59-b2097830d03e',
            'name'  => 'admin',
            'label' => 'Administrator',
        ],
        [
            'id'    => '52e06e50-7873-4e0d-97e7-58d9433e7e4f',
            'name'  => 'customer',
            'label' => 'Customer',
        ],
    ];

    public function run(): void
    {
        foreach (self::DATA as $value) {
            DB::table('roles')->updateOrInsert(Arr::only($value, ['id']), $value);
        }
    }
}
