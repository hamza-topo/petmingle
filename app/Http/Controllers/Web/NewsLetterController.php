<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MailList;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NewsLetterController extends Controller
{
    /**
     * Add new mail to mail list
     *
     * @return void
     */
    public function subscribe(Request $request)
    {
        $this->validate($request, [
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        try {
            MailList::create($request->all());

            return redirect()->back();
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return abort(500);
        }
    }
}
