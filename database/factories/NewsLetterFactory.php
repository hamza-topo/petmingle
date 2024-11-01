<?php

namespace Database\Factories;

use App\Enums\Pages;
use App\Models\NewsLetter;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewsLetterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = NewsLetter::class;
    
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'type' => rand(0,2),
            'species_id' => rand(1,3),
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
            'active' => $this->faker->boolean,
        ];
    }
}
