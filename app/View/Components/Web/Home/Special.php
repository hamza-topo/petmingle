<?php

namespace App\View\Components\Web\Home;

use Illuminate\View\Component;
use Illuminate\Support\Collection;
use App\Repositories\PetRepository;

class Special extends Component
{
    public Collection $pets;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(protected PetRepository $petRepository)
    {
        $this->pets = $this->petRepository->getLastNewPets();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.web.home.special');
    }
}
