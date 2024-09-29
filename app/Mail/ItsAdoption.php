<?php

namespace App\Mail;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ItsAdoption extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        protected Pet $pet,
        protected User $owner,
        protected User $newOwner
    ) {
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
            \__('🎉 You\'re Adoption! Dive into the Adventure with ' . $this->pet->name . '! 🚀')
        )
            ->view('emails.adoption')
            ->with('pet', $this->pet)
            ->with('owner', $this->owner)
            ->with('newOwner', $this->newOwner)
        ;
    }
}
