<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware: controleer of de ingelogde gebruiker de juiste rol heeft.
 *
 * Gebruik in routes: ->middleware(['auth', 'role:admin,manager'])
 */
class CheckRole
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'U moet ingelogd zijn om deze pagina te bekijken.');
        }

        $user = Auth::user();

        if (strtolower($user->status ?? '') !== 'actief') {
            Auth::logout();

            return redirect()->route('login')->with('error', 'Uw account is niet actief.');
        }

        if (! in_array($user->role, $roles, true)) {
            return redirect()->route('home')->with('error', 'U heeft geen toegang tot deze pagina.');
        }

        return $next($request);
    }
}
