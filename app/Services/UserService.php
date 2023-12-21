<?php

namespace App\Services;

use App\Mail\Welcome;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserService
{
    public function welcome(User $user)
    {
        Mail::to($user->email)->queue(new Welcome($user));
    }
}
