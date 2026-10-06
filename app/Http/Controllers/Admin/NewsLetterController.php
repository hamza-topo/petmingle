<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MailList;
use App\Repositories\NewsLetterRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NewsLetterController extends Controller
{
    public function __construct(protected NewsLetterRepository $newsLetterRepository) {}

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

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
