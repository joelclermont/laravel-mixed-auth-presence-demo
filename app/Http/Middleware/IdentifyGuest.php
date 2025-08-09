<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyGuest
{
    /**
     * This is just a proof of concept to show you might identify unauthenticated users
     * in a stable way, based on session data. In a real application, you would likely
     * need something more robust to prevent collisions and handle any security implications.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $request->session()->forget(['guest_id', 'guest_name']);
            return $next($request);
        }

        if (! $request->session()->has('guest_id')) {
            // Generate a unique integer ID starting with 999
            $guestId = 999000000 + random_int(1, 999999);
            $request->session()->put('guest_id', $guestId);
            $request->session()->put('guest_name', 'Guest ' . random_int(1000, 9999));
        }

        return $next($request);
    }
}
