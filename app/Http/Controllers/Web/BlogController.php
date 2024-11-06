<?php

namespace App\Http\Controllers\Web;

use App\Enums\App;
use App\Enums\Pages;
use App\Repositories\SeoRepository;
use App\Http\Controllers\Controller;

use App\Repositories\BlogRepository;

class BlogController extends Controller
{
    public function __construct(
        protected BlogRepository $blogRepostory,
        protected SeoRepository $seoRepository
    ) {}

    public function __invoke()
    {
        $seo = $this->seoRepository->getByKey(Pages::BLOGS->value);
        $blogs = $this->blogRepostory->take();

        return view('web.blog', compact('seo', 'blogs'));
    }
}
