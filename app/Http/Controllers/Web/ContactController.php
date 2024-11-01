<?php

namespace App\Http\Controllers\Web;

use App\Enums\Pages;
use App\Http\Controllers\Controller;
use App\Events\Contact as ContactEvent;
use App\Http\Requests\Web\Contact\Store;
use App\Mail\Contact;
use App\Models\Contact as ModelsContact;
use App\Repositories\SeoRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function __construct(protected SeoRepository $seoRepository) {}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $seo = $this->seoRepository->getByKey(Pages::CONTACT->value);
        return view('web.contact', compact('seo'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Store $request)
    {
        //TODO:validate request
        //validate request is done
        try {
            Log::info('send email contact ...');
            Mail::to(env('MAIL_CONTACT_US'))->queue(new Contact($request->all()));
            ModelsContact::create($request->all());

            return redirect(route('contact'))->with('success', __('Your message has been sent successfully!'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to send your message. Please try again later.'));
            Log::error('failed send email contact..:[ ' . $e->getMessage() . ' ]');
        }
    }
}
