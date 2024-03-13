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
            'name' => 'For my self',
        ],
        [
            'id'   => '4db63e30-f387-4925-842d-0c3dcae4632f',
            'name' => 'For my parents',
        ],
        [
            'id'   => 'b18697a6-fb00-4386-bbd0-ba4ee3037f20',
            'name' => 'For my spouse',
        ],
        [
            'id'   => '193e2840-bd8c-49c3-8965-e369bf5c86ed',
            'name' => 'For my children',
        ],
        [
            'id'   => '1d759e7b-2229-4bb9-98f3-8129bfc7dd51',
            'name' => 'For my friends/others',
        ],
    ];

    public function run(): void
    {
        foreach (self::DATA as $value) {
            DB::table('designations')->updateOrInsert(Arr::only($value, ['id']), $value);
        }
    }
}
