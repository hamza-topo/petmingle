<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Repositories\NewsLetterRepository;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function __construct(protected NewsLetterRepository $newsRepository) {}

    public function __invoke()
    {
        return view('web.blog');
    }
}
