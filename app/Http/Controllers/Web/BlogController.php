<?php

namespace App\Http\Controllers\Web;

use App\Enums\Pages;
use App\Http\Controllers\Controller;
use App\Repositories\NewsLetterRepository;
use App\Repositories\SeoRepository;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function __construct(
        protected NewsLetterRepository $newsRepository,
        protected SeoRepository $seoRepository
    ) {}

    public function __invoke()
    {
        $seo = $this->seoRepository->getByKey(Pages::BLOGS->value);

        return view('web.blog', compact('seo'));
    }
}
