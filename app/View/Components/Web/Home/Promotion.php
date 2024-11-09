<?php

namespace App\View\Components\Web\Home;

use Illuminate\View\Component;
use App\Repositories\ComponentRepository;
use App\Enums\Component as EnumComponent;
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
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.web.home.promotion');
    }
}
