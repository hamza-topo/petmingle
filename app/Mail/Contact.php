<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class Contact extends Mailable
{
    use Queueable, SerializesModels;

    public array $mail = [];
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $mail = [])
    {
        $this->mail = $mail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        try {

            Log::info('Auto reply contact us!');
            return $this->subject(
                config('app.name') . ':' . \__('Contact Us') . $this->mail['name'] ?? ''
            )->with('user', $this->mail['name'])->view('emails.contact');
        } catch (\Exception $e) {
            Log::error('Auto reply contact us:  KO [ ' . $e->getMessage() . ' ]');
        }
    }
}
