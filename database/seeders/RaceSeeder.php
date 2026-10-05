<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dogsSpeciesId = DB::table('species')
            ->where('name', 'Dogs')
            ->value('id');

        if ($dogsSpeciesId === null) {
            throw new RuntimeException(
                'Dogs species must be seeded before dog breeds.'
            );
        }

        $breeds = [
            'Labrador Retriever',
            'German Shepherd',
            'Golden Retriever',
            'Bulldog',
            'Beagle',
            'Poodle',
            'Rottweiler',
            'Dachshund',
            'Boxer',
        ];

        foreach ($breeds as $breed) {
            DB::table('races')->updateOrInsert(
                [
                    'species_id' => $dogsSpeciesId,
                    'name' => $breed,
                ],
                [
                    'deleted_at' => null,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
