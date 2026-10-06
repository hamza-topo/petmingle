<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Contact extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $mail) {}

    public function build(): self
    {
        return $this->subject(config('app.name').': '.$this->mail['subject'])
            ->replyTo($this->mail['email'], $this->mail['name'])
            ->view('emails.contact');
    }
}
