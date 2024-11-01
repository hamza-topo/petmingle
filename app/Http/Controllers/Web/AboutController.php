<?php

namespace App\Http\Controllers\Web;

use App\Enums\Pages;
use App\Http\Controllers\Controller;
use App\Repositories\SeoRepository;

class AboutController extends Controller
{
    public function __construct(protected SeoRepository $seoRepository) {}
    public function __invoke()
    {
        $seo = $this->seoRepository->getByKey(Pages::ABOUT->value);
        
        return view('web.about', compact('seo'));
    }
}
