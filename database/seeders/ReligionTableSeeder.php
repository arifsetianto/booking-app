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
            'name' => 'Islam',
        ],
        [
            'id'   => 'efc93b73-8c0f-4ee5-aca0-2db75945e122',
            'name' => 'Christian',
        ],
        [
            'id'   => '8dd01c05-2425-4ed0-a8c1-dc5a2bb80c45',
            'name' => 'Protestant',
        ],
        [
            'id'   => '5cec9732-4885-4684-be09-461a9f843e31',
            'name' => 'Catholic',
        ],
        [
            'id'   => 'c1eddc1e-abcb-4a7a-ab8f-e61665b0734c',
            'name' => 'Confucius',
        ],
        [
            'id'   => '009ee789-741d-4ded-a34c-1c798fe8756a',
            'name' => 'Buddha',
        ],
        [
            'id'   => '60bb7834-da49-4308-853b-d0a75048e04e',
            'name' => 'Hindu',
        ],
        [
            'id'   => '0a7bc180-49de-4534-8ce2-7034fa80707b',
            'name' => 'Jewish',
        ],
    ];

    public function run(): void
    {
        foreach (self::DATA as $value) {
            DB::table('religions')->updateOrInsert(Arr::only($value, ['id']), $value);
        }
    }
}
