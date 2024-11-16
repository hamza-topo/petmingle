<?php

namespace App\Http\Controllers\Web;

use App\Enums\Pages;
use Illuminate\Http\Request;
use App\Repositories\SeoRepository;
use App\Http\Controllers\Controller;
use App\Repositories\BlogRepository;

class PrivacyPolicyController extends Controller
{
    public function __construct(
        protected SeoRepository $seoRepository,
        protected BlogRepository $blogRepositoryRepository,
    ) {}
    public function __invoke()
    {
        $slug = __('privacy-policy');
        $seo = $this->seoRepository->getByKey(Pages::PRIVACY_POLICY->value);
        $blog = $this->blogRepositoryRepository->getBySlug($slug, app()->getLocale());

        return view('web.privacy-policy', compact('seo', 'blog'));
    }
}
