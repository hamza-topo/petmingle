<?php

namespace App\Services;

use App\Enums\Component;
use App\Models\Component as MComponent;
use Illuminate\Support\Collection;

class ComponentService
{
    public function format(Collection $components): Collection
    {
        $cases = collect(Component::cases())->pluck('name', 'value')->toArray();
        if (empty($components)) {
            return $cases;
        }

        foreach ($components as $component) {
            $cases[$component->name] = $component;
        }

        return collect($cases)->map(function ($case, $key) {

            if (! $case instanceof MComponent) {
                $case = new MComponent;
                $case->name = $key;
            }

            return $case;
        });
    }
}
