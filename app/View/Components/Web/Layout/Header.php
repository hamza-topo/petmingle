<?php

namespace App\View\Components\Web\Layout;

use Illuminate\View\Component;
use App\Enums\Header as EnumHeader;
use App\Enums\Component as EnumComponent;
use App\Repositories\ComponentRepository;
use App\Models\Component as ModelComponent;

class Header extends Component
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
        $menus = array_filter(EnumHeader::MENUS, function ($menu) {
            if (!empty($menu['show']) && $menu['show']) {
                return $menu;
            }
        });

        return view('components.web.layout.header', compact('menus'));
    }
}
