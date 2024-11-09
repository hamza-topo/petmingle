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

    /**
     * Index page to display the list of blogs
     *
     * @return void
     */
    public function index()
    {
        $seo = $this->seoRepository->getByKey(Pages::BLOGS->value);
        $blogs = $this->blogRepostory->take();

        return view('web.blog', compact('seo', 'blogs'));
    }

    /**
     * Display The detail page
     *
     * @param string $slug
     * @return void
     */
    public function read(string $slug)
    {
        $seo = $this->seoRepository->getByKey(Pages::BLOGS->value);
        $blog = $this->blogRepostory->getBySlug($slug, app()->getLocale());
        $randoms = $this->blogRepostory->random();

        return view('web.detail-blog', compact('seo', 'blog', 'randoms'));
    }
}
