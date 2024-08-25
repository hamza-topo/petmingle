<?php

namespace Database\Seeders;

use App\Enums\Pet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pets = $this->pets();
        DB::table('pets')->insert($pets);
    }

    private function pets(): array
    {

        $pets = [];
        for ($i = 0; $i < 10; $i++) {
            $pets[$i] = [
                'user_id' => rand(1, 10),
                'species_id' => rand(1, 10),
                'name' => Str::random(10),
                'race_id' => rand(1, 10),
                'age' => rand(1, 50),
                'sexe' => rand(Pet::FEMALE, Pet::MALE),
                'color' => $this->color(),
                'images' => json_encode(array_fill(0, rand(0, 5), 'https://dog.ceo/api/breeds/image/random')),
                'about' => '<p>' . Str::random(100) . ' </p><p> ' . Str::random(rand(25, 50)) . '</p>',
            ];
        }
        return $pets;
    }

    /**
     * methode to generate random color
     *
     * @return string
     */
    private  function color(): string
    {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }
}
