<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index()
    {
        return view('emails.index',['pet'=> Pet::find(1)]);
    }
}
