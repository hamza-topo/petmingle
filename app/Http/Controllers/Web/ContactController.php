<?php

namespace App\Http\Controllers\Web;

use App\Enums\Pages;
use App\Http\Controllers\Controller;
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
     * @return Response
     */
    public function index()
    {
        $seo = $this->seoRepository->getByKey(Pages::CONTACT->value);

        return view('web.contact', compact('seo'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Store $request)
    {
        try {
            Log::info('send email contact ...');
            Mail::to(config('mail.contact_address'))->queue(new Contact($request->validated()));
            ModelsContact::create($request->validated());

            return redirect(route('contact'))->with('success', __('Your message has been sent successfully!'));
        } catch (\Exception $e) {
            Log::error('failed send email contact..:[ '.$e->getMessage().' ]');

            return redirect()->back()->with('error', __('Failed to send your message. Please try again later.'));
        }
    }
}
