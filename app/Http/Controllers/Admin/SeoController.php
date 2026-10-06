<?php

namespace App\Http\Controllers\Admin;

use App\Enums\App;
use App\Enums\Pages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Seo\Store;
use App\Http\Requests\Admin\Seo\Update;
use App\Repositories\SeoRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SeoController extends Controller
{
    public function __construct(protected SeoRepository $seoRepository) {}

    public function index(): View
    {
        $pages = Pages::cases();
        $seos = $this->seoRepository->all();

        return view('admin.seo.index', compact('pages', 'seos'));
    }

    public function create(): View
    {
        $pages = $this->seoRepository->getAvvaillable();
        $langs = App::LOCALES;

        return view('admin.seo.create', compact('pages', 'langs'));
    }

    public function store(Store $request): RedirectResponse
    {
        $this->seoRepository->create($request->validated());

        return redirect()->route('admin.seo.index');
    }

    public function edit(int $pageId): View
    {
        $page = $this->seoRepository->getById($pageId);
        $langs = App::LOCALES;

        return view('admin.seo.edit', compact('page', 'langs'));
    }

    public function update(Update $request, int $id): RedirectResponse
    {
        $this->seoRepository->update($id, $request->validated());

        return redirect()->route('admin.seo.index');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->seoRepository->delete($id);

        return redirect()->route('admin.seo.index');
    }
}
