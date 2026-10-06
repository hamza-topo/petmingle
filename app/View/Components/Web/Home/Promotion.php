<?php

namespace App\View\Components\Web\Home;

use App\Enums\Component as EnumComponent;
use App\Repositories\ComponentRepository;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Promotion extends Component
{
    public $component;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(protected ComponentRepository $componentRepository)
    {
        $this->component = $this->componentRepository->getByName(EnumComponent::HEADER->value);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|\Closure|string
     */
    public function render()
    {
        return view('components.web.home.promotion');
    }
}
