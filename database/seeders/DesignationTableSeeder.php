<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class DesignationTableSeeder extends Seeder
{
    const DATA = [
        [
            'id'   => 'c7507e4f-8230-4d7b-8993-bab6d96490a7',
            'name' => 'Parents',
        ],
    ];

    public function run(): void
    {
        foreach (self::DATA as $value) {
            DB::table('designations')->updateOrInsert(Arr::only($value, ['id']), $value);
        }
    }
}
