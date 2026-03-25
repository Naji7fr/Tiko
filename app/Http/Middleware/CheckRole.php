<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'U moet ingelogd zijn om deze pagina te bekijken.');
        }

        $user = Auth::user();

        // Check if user is active
        if (strtolower($user->status ?? '') !== 'actief') {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Uw account is niet actief.');
        }

        // Check if user has required role
        if (!in_array($user->role, $roles)) {
            return redirect()->route('home')->with('error', 'U heeft geen toegang tot deze pagina.');
        }

        return $next($request);
    }
}

