<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *     This middleware checks if the current user has admin privileges.
     * If the user is not authenticated or does not have admin rights,
     * the request is aborted with a 403 Forbidden response.
     *
     * @author Youssef tamri <yousseftam100@gmail.com>
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (! auth()->check()) {
            abort(401, 'Unauthorized');
        }
        if (! auth()->user()->is_admin) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}
