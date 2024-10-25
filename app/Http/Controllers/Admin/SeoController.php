<?php

namespace App\Http\Controllers\Admin;

use App\Enums\App;
use App\Enums\Pages;
use App\Http\Controllers\Controller;
use App\Repositories\SeoRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\TryCatch;

class SeoController extends Controller
{
    /**
     * @property-read SeoRepository
     *
     * @param SeoRepository $seoRepository
     */
    public function __construct(protected SeoRepository $seoRepository) {}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pages = Pages::cases();
        $seos = $this->seoRepository->all();
        return view('admin.seo.index', compact('pages', 'seos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pages = $this->seoRepository->getAvvaillable();
        $langs = App::LOCALES;
        return view('admin.seo.create', compact('pages', 'langs'));
    }

    /**
     *    The function `store` attempts to create a new SEO record using data from the request, logging an
     * | error if any exception occurs.
     * 
     * @param Request 
     * | request The `Request ` parameter in the `store` function represents an
     * | HTTP request that contains all the data submitted by the user. In this context, it is used to
     * | retrieve all input data from the request to create a new SEO page using the `seoRepository`.
     *  @return void
     */
    public function store(Request $request)
    {
        try {
            $this->seoRepository->create($request->all());
            
            return redirect(route('admin.seo.index'));
        } catch (\Exception $e) {
            Log::error('error Updating seo page: ' . $e->getMessage());
        }
    }

    /**
     * Display Edit seo view
     *
     * @param integer $pageId
     * @return void
     */
    public function edit(int $pageId)
    {
        $page = $this->seoRepository->getById($pageId);
        $pages = $this->seoRepository->getAvvaillable();
        $langs = App::LOCALES;
        return view('admin.seo.edit', compact('page', 'langs', 'pages'));
    }

    public function update(Request $request, int $id)
    {
        try {
            $this->seoRepository->update($id, $request->all());

            return redirect(route('admin.seo.index'));
        } catch (\Exception $e) {
            Log::error('error Updating: ' . $e->getMessage());
        }
    }
}
