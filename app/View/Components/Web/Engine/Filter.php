<?php

namespace App\View\Components\Web\Engine;

use App\Repositories\RaceRepository;
use App\Repositories\SpeciesRepository;
use Illuminate\View\Component;

class Filter extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        protected SpeciesRepository $speciesRepository,
        protected RaceRepository $raceRepository,
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $species = $this->speciesRepository->all()->pluck('name', 'id');

        return view('components.web.engine.filter', compact('species'));
    }
}
