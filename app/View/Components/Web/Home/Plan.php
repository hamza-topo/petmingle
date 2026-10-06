<?php

namespace App\View\Components\Web\Home;

use App\Enums\Component as EnumComponent;
use App\Models\Component as ModelComponent;
use App\Repositories\ComponentRepository;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Plan extends Component
{
    public ModelComponent $component;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(protected ComponentRepository $componentRepository)
    {
        $this->component = $this->componentRepository->getByName(EnumComponent::PLAN->value) ?? new ModelComponent;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|\Closure|string
     */
    public function render()
    {
        return view('components.web.home.plan');
    }
}
