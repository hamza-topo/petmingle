<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

/**
 * HomeController
 */
class HomeController extends Controller
{
    /**
     * The index function returns the view 'admin.index'.
     *
     * @author Topo <hamzaaitsidisaid.11@gmail.com>
     * @return A view named 'admin.index' is being returned.
     * @cre
     */
    public function index()
    {
        //THIS METHOD IS CREATED NOW 
        return view('admin.index');
    }

}
