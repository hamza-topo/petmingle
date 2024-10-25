<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Repositories\PetRepository;
use Illuminate\Http\Request;

class EngineController extends Controller
{

    public function __construct(protected PetRepository $petRepository) {}

    public function index()
    {
        $pets = $this->petRepository->all();

        return view('web.search', compact('pets'));
    }
}
