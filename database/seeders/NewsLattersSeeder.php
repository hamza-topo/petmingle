<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use App\Enums\NewsLetter;
use App\Models\Species;

class NewsLattersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @author youssef tamri <yousseftam100@gmail.com>
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $numberOfRecords = 10;
        for ($i = 0; $i < $numberOfRecords; $i++) {
            DB::table('news_letters')->insert([
                'type' => $faker->randomElement([
                    NewsLetter::MOBILE,
                    NewsLetter::EMAIL,
                    NewsLetter::ALL
                ]),
                'species_id' => $faker->optional()->randomElement(Species::pluck('id')->toArray()),
                'title' => $faker->sentence,
                'content' => $faker->paragraphs(3, true),
                'active' => $faker->boolean,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
