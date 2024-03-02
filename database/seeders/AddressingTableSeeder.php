<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class AddressingTableSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedRegions();
        $this->seedCities();
        $this->seedDistricts();
        $this->seedSubDistricts();
    }

    private function seedRegions(): void
    {
        $file = __DIR__ . '/data/thaiquran_regions.csv';
        $contents = file_get_contents($file);

        $arr = collect(str_getcsv($contents, "\n"))
            ->map(
                function (string $item) {
                    return str_getcsv($item, ',');
                }
            )
            ->map(
                function (array $item) {
                    return [
                        'id'        => $item[0],
                        'name'      => $item[1],
                    ];
                }
            );

        DB::table('regions')->insert($arr->toArray());
    }

    private function seedCities(): void
    {
        $file = __DIR__ . '/data/thaiquran_cities.csv';
        $contents = file_get_contents($file);

        $arr = collect(str_getcsv($contents, "\n"))
            ->map(
                function (string $item) {
                    return str_getcsv($item, ',');
                }
            )
            ->map(
                function (array $item) {
                    return [
                        'id'        => $item[0],
                        'region_id' => $item[1],
                        'en_name'   => $item[2],
                        'th_name'   => $item[3],
                    ];
                }
            );

        DB::table('cities')->insert($arr->toArray());
    }

    private function seedDistricts(): void
    {
        $file = __DIR__ . '/data/thaiquran_districts.csv';
        $contents = file_get_contents($file);

        $arr = collect(str_getcsv($contents, "\n"))
            ->map(
                function (string $item) {
                    return str_getcsv($item, ',');
                }
            )
            ->map(
                function (array $item) {
                    return [
                        'id'        => $item[0],
                        'city_id'   => $item[1],
                        'en_name'   => $item[2],
                        'th_name'   => $item[3],
                    ];
                }
            );

        DB::table('districts')->insert($arr->toArray());
    }

    private function seedSubDistricts(): void
    {
        $file = __DIR__ . '/data/thaiquran_sub_districts.csv';
        $contents = file_get_contents($file);

        $arr = collect(str_getcsv($contents, "\n"))
            ->map(
                function (string $item) {
                    return str_getcsv($item, ',');
                }
            )
            ->map(
                function (array $item) {
                    return [
                        'id'          => $item[0],
                        'district_id' => $item[1],
                        'en_name'     => $item[2],
                        'th_name'     => $item[3],
                        'zip_code'    => $item[4],
                    ];
                }
            );

        DB::table('sub_districts')->insert($arr->toArray());
    }
}
