<?php

namespace App\View\Components\Web\Home\Blog;

use App\Repositories\NewsLetterRepository;
use Illuminate\View\Component;

class SideSection extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(protected NewsLetterRepository $newsRepository) {}


    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $current_news = $this->newsRepository->take();
        return view('components.web.home.blog.side-section' , compact('current_news'));
    }
}
