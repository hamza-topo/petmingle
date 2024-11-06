<?php

namespace App\View\Components\Web\Home\Blog;

use Illuminate\View\Component;
use Illuminate\Support\Collection;
use App\Repositories\NewsLetterRepository;

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
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.web.home.blog.side-section');
    }
}
