<?php

namespace App\View\Components\Web\Layout;

use App\Models\Seo;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use stdClass;

class Meta extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(public stdClass|Seo|null $seo) {}

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|\Closure|string
     */
    public function render()
    {
        return view('components.web.layout.meta');
    }
}
