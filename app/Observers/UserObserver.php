<?php

namespace App\Observers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    public function __construct(
        protected UserService $userService,

    ) {
    }
    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        try {
            Log::error('start sending welcome mail ');

            $this->userService->welcome($user);
        } catch (\Exception $e) {
            Log::error('sending welcome mail is matching failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle the User "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        try {
            Log::error('start sending goodBye mail ');
            $this->userService->goodBye($user);
        } catch (\Exception $e) {
            Log::error('sending goodBye mail failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle the User "restored" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        try {
            Log::error('start sending welcomeBack mail ');
            $this->userService->welcomeBack($user);
        } catch (\Exception $e) {
            Log::error('sending welcomeBack mail failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle the User "force deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}
