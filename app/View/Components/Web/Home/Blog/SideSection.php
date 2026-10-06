<?php

namespace App\View\Components\Web\Home\Blog;

use App\Repositories\NewsLetterRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class SideSection extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(protected NewsLetterRepository $newsRepository, public Collection $blogs) {}

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|\Closure|string
     */
    public function render()
    {
        return view('components.web.home.blog.side-section');
    }
}
