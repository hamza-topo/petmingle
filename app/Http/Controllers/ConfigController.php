<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function toggleShowTrashed(Request $request)
    {
        // Set the session variable based on the request
        session(['show_trashed' => $request->get('trashed') == 1]);
        return redirect()->back();
    }
}
