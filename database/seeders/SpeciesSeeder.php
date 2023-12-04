<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SpeciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('species')->insert(
            [
                [
                    'name' => 'Dogs',
                    'description' => 'Dogs are known for their loyalty and companionship. They come in various breeds, sizes, and temperaments, making it possible to find a dog that fits well with your lifestyle.'
                ],
                [
                    'name' => 'Cats',
                    'description' => 'Cats are independent yet affectionate pets. They are known for their playful behavior and are often kept indoors as well as outdoors.'
                ],
                [
                    'name' => 'Fish',
                    'description' => 'Fish, such as goldfish or bettas, make great pets for those who enjoy observing aquatic life. Proper aquarium setup and water maintenance are essential for their well-being.'
                ],
                [
                    'name' => 'Birds',
                    'description' => 'Birds can be entertaining and social companions. They require proper cages, a balanced diet, and mental stimulation.'
                ],
                [
                    'name' => 'Rabbits',
                    'description' => 'Rabbits are social animals that can be kept indoors or in secure outdoor enclosures. They require proper diet, space, and attention.'
                ],
                [
                    'name' => 'Guinea Pigs',
                    'description' => 'Guinea pigs are small, social rodents that are relatively easy to care for. They need a spacious cage, a balanced diet, and companionship.'
                ],
                [
                    'name' => 'Hamsters',
                    'description' => 'Hamsters are small, nocturnal rodents that are suitable for small living spaces. They need a comfortable cage, proper bedding, and a balanced diet.'
                ],
                [
                    'name' => 'Turtles',
                    'description' => 'Turtles are reptiles that can be kept in aquariums. They require a suitable basking area, proper lighting, and a well-maintained aquatic environment.'
                ],
                [
                    'name' => 'Ferrets',
                    'description' => 'Ferrets are playful and curious animals. They need a secure living space, a balanced diet, and mental stimulation.'
                ],
                [
                    'name' => 'Gerbils',
                    'description' => 'Gerbils are small, social rodents that are easy to care for. They require a suitable cage, proper bedding, and a balanced diet.'
                ]
            ]
        );
    }
}
