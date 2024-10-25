<?php

namespace Database\Factories;

use App\Enums\Pages;
use App\Models\Seo;
use Illuminate\Database\Eloquent\Factories\Factory;

class SeoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Seo::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        return [
            'key' => Pages::ABOUT,
            'meta' => ['description' => ['fr' => 'meta', 'en' => 'meta', 'es' => 'meta']],
            'title' => ['fr' => 'titre', 'en' => 'title', 'es' => 'título']
        ];
    }
}
