<?php

namespace App\Mail;

use App\Models\Pet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ItsAMatch extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public Pet $pet, public Pet $fromPet)
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(
            \__('🎉 You\'re Matched! Dive into the Adventure with '. $this->pet->name .'! 🚀')
        )
            ->view('emails.match')
            ->with('pet', $this->pet)
            ->with('fromPet', $this->fromPet)
            ;
    }
}
