<?php

namespace App\Http\Controllers\Web;

use App\Enums\Pages;
use App\Http\Controllers\Controller;
use App\Repositories\PetRepository;
use App\Repositories\SeoRepository;
use Illuminate\Http\Request;

class EngineController extends Controller
{

    public function __construct(
        protected PetRepository $petRepository,
        protected SeoRepository $seoRepository,
    ) {}

    public function index()
    {
        $pets = $this->petRepository->all();
        $seo = $this->seoRepository->getByKey(Pages::ENGINE->value);
        return view('web.search', compact('pets', 'seo'));
    }

    public function show(string $slug, int $id)
    {
        try {
            $result = $this->petRepository->getById($id);

            return view('web.search_detail', compact('result'));
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
        }
    }
}
