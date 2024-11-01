<?php

namespace App\View\Components\Web\Home;

use Illuminate\View\Component;
use App\Repositories\ComponentRepository;
use App\Models\Component as ModelComponent;
use App\Enums\Component as EnumComponent;
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
        $this->component = $this->componentRepository->getByName(EnumComponent::PLAN->value);
    }
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.web.home.plan');
    }
}
