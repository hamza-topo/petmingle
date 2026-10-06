<?php

namespace App\Mail;

use App\Models\NewsLetter;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsLetterMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public NewsLetter $newsLetter
    ) {}

    public function build(): self
    {
        return $this
            ->subject($this->newsLetter->title)
            ->view('emails.newsletter')
            ->with('newsLetter', $this->newsLetter);
    }
}
