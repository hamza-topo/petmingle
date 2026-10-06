<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MailList;
use Illuminate\Http\Response;

class NewsLetterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $mailList = MailList::orderBy('id', 'desc')->paginate();

        return view('admin.mail-list.index', compact('mailList'));
    }
}
