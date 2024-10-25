<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('races')->insert(
            [
                [
                    'species_id' => 1,
                    'name' => 'Labrador Retriever',
                ],
                [
                    'species_id' => 1,
                    'name' => 'German Shepherd',
                ],
                [
                    'species_id' => 1,
                    'name' => 'Golden Retriever',
                ],
                [
                    'species_id' => 1,
                    'name' => 'Bulldog',
                ],
                [
                    'species_id' => 1,
                    'name' => 'Beagle',
                ],
                [
                    'species_id' => 1,
                    'name' => 'Guinea Pigs',
                ],
                [
                    'species_id' => 1,
                    'name' => 'Poodle',
                ],
                [
                    'species_id' => 1,
                    'name' => 'Rottweiler',
                ],
                [
                    'species_id' => 1,
                    'name' => 'Dachshund',
                ],
                [
                    'species_id' => 1,
                    'name' => 'Boxer',
                ]
            ]
        );
    }
}
