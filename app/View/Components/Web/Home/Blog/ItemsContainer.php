<?php

namespace App\View\Components\Web\Home\Blog;

use App\Repositories\NewsLetterRepository;
use Illuminate\View\Component;

class ItemsContainer extends Component
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
        $news = $this->newsRepository->paginate(request()->get('paginate'));
        return view('components.web.home.blog.items-container', compact('news'));
    }

}
