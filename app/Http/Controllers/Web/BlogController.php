<?php

namespace App\Http\Controllers\Web;

use App\Enums\Pages;
use App\Http\Controllers\Controller;
use App\Repositories\BlogRepository;
use App\Repositories\SeoRepository;
use stdClass;

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
        $blogs = $this->blogRepostory->paginate(true);
        $exludedSlugs = collect(Pages::cases())->pluck('value')->map(function ($slug) {
            return strtolower(slugify($slug));
        })->toArray();

        return view('web.blog', compact('seo', 'blogs', 'exludedSlugs'));
    }

    /**
     * Display The detail page
     *
     * @return void
     */
    public function read(string $slug)
    {
        $seo = new stdClass;
        $blog = $this->blogRepostory->getBySlug($slug, app()->getLocale());
        $seo->meta['description'][app()->getLocale()] = generateTextPreview($blog->content[app()->getLocale()] ?? '');
        $seo->meta['keywords'][app()->getLocale()] = generateTextPreview($blog->content[app()->getLocale()] ?? '');
        $seo->title[app()->getLocale()] = $blog->title[app()->getLocale()] ?? '';
        $randoms = $this->blogRepostory->random();

        return view('web.detail-blog', compact('seo', 'blog', 'randoms'));
    }
}
