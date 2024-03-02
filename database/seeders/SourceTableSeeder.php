<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class SourceTableSeeder extends Seeder
{
    const DATA = [
        [
            'id'   => '6d20f692-5e40-42b2-a49a-b953308f8142',
            'name' => 'Review/Testimony in Social Media',
        ],
        [
            'id'   => 'd3990280-1c0d-4304-8193-ab51f64a9950',
            'name' => 'Instagram',
        ],
        [
            'id'   => '0583fd2f-8ac6-4b63-b55f-b51990f68bfd',
            'name' => 'Facebook',
        ],
        [
            'id'   => '51e2204d-35a2-4975-b5b9-2f666ffc7ae9',
            'name' => 'Google',
        ],
        [
            'id'   => '1dd7f566-d795-4bd9-b6a6-f1c125967fda',
            'name' => 'Event',
        ],
        [
            'id'   => '4ba66462-a8bf-496f-bc3c-d13669c3b06e',
            'name' => 'Masjid/Community',
        ],
        [
            'id'   => 'eb5d61f1-ee29-4590-9607-be3306d09328',
            'name' => 'Family/Friend',
        ],
        [
            'id'   => '15e1cf08-a8a6-475a-bfaf-610bed3f151a',
            'name' => 'Tiktok',
        ],
        [
            'id'   => 'bd3f9c19-f065-402c-8a59-6527788f10ac',
            'name' => 'Other',
        ],
    ];

    public function run(): void
    {
        foreach (self::DATA as $value) {
            DB::table('sources')->updateOrInsert(Arr::only($value, ['id']), $value);
        }
    }
}
