<?php

namespace App\Http\Controllers\Web;

use App\Enums\Pages;
use App\Repositories\SeoRepository;
use App\Http\Controllers\Controller;
use App\Repositories\BlogRepository;

class AboutController extends Controller
{
    public function __construct(
        protected SeoRepository $seoRepository,
        protected BlogRepository $blogRepositoryRepository,
    ) {}
    public function __invoke()
    {
        $slug = __('about');
        $seo = $this->seoRepository->getByKey(Pages::ABOUT->value);
        $blog = $this->blogRepositoryRepository->getBySlug($slug, app()->getLocale());

        return view('web.about', compact('seo', 'blog'));
    }
}
