<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsLatter extends Mailable
{
    use Queueable, SerializesModels;

    public array $news = [];
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $news = [])
    {
        $this->news = $news;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('view.name');
    }
}
