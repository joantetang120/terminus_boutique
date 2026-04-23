<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class GhostAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user has ghost.view permission
        if (!$request->user()->hasPermissionTo('ghost.view')) {
            abort(403, 'Vous n\'avez pas la permission d\'accéder aux factures fantômes.');
        }

        // Check if ghost access is authenticated via password
        if (!Session::has('ghost_access_verified')) {
            // Store intended URL for redirect after password verification
            Session::put('ghost_intended_url', $request->url());
            return redirect()->route('ghost.password');
        }

        return $next($request);
    }
}
