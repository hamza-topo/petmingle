<?php

namespace App\Mappers;

class Pet
{
    protected $pet = [];

    public function __invoke(array $request): self
    {
        $this->pet = [
            'user_id' => $request['user_id'],
            'species_id' => $request['species_id'],
            'race_id' => $request['race_id'],
            'name' => $request['name'],
            'sexe' => $request['sexe'],
            'age' => $request['age'] ?? null,
            'images' => $request['images'] ?? [],
            'color' => $request['color'] ?? '',
            'about' => $request['about'] ?? '',
        ];

        return $this;
    }

    public function toArray(): array
    {
        return $this->pet;
    }
}
