<?php

namespace App\View\Components\Web\Layout;

use Illuminate\View\Component;
use App\Models\Seo;

class Meta extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(public ?Seo $seo)
    {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.web.layout.meta');
    }
}
