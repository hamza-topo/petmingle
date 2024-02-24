<?php

namespace App\Services;

use App\Mail\GoodBye;
use App\Mail\Welcome;
use App\Mail\WelcomeBack;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserService
{
    public function welcome(User $user)
    {
        Mail::to($user->email)->queue(new Welcome($user));
    }

    public function goodBye(User $user)
    {
        Mail::to($user->email)->queue(new GoodBye($user));
    }

    public function welcomeBack(User $user)
    {
        Mail::to($user->email)->queue(new WelcomeBack($user));
    }
}
