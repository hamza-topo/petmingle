<?php

namespace App\View\Components\Web\Engine;

use App\Models\Pet;
use Illuminate\View\Component;

class Item extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(public Pet $pet)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.web.engine.item');
    }
}
