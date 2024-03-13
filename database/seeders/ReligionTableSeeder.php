<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class ReligionTableSeeder extends Seeder
{
    const DATA = [
        [
            'id'   => 'acd305fe-b072-4075-8fd3-1991468b059f',
            'name' => 'Muslim',
        ],
        [
            'id'   => 'efc93b73-8c0f-4ee5-aca0-2db75945e122',
            'name' => 'Mualaf',
        ],
        [
            'id'   => '8dd01c05-2425-4ed0-a8c1-dc5a2bb80c45',
            'name' => 'Non-muslim interested in quran',
        ],
    ];

    public function run(): void
    {
        foreach (self::DATA as $value) {
            DB::table('religions')->updateOrInsert(Arr::only($value, ['id']), $value);
        }
    }
}
