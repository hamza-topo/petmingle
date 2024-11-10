<?php

namespace App\View\Components\Web\Layout;

use stdClass;
use App\Models\Seo;
use Illuminate\View\Component;

class Meta extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(public stdClass|Seo|null $seo)
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
