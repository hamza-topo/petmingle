<?php

namespace App\Http\Controllers\Web;

use App\Enums\Pages;
use App\Http\Controllers\Controller;
use App\Repositories\NewsLetterRepository;
use App\Repositories\SeoRepository;
use App\Repositories\BlogRepository;

use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function __construct(
        protected BlogRepository $blogRepostory,
        protected SeoRepository $seoRepository
    ) {}

    public function __invoke()
    {
        $seo = $this->seoRepository->getByKey(Pages::BLOGS->value);
        $blogs = $this->blogRepostory->all();
       
        return view('web.blog', compact('seo', 'blogs'));
    }
}
