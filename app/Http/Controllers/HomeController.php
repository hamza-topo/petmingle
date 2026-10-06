<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;

class HomeController extends Controller
{
    /**
     * Show the application Front Home page.
     *
     * @return Renderable
     */
    public function index()
    {
        return view('welcome');
    }
}
