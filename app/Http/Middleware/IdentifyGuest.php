<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class IdentifyGuest
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $request->session()->forget(['guest_id', 'guest_name']);
            return $next($request);
        }

        if (! $request->session()->has('guest_id')) {
            $request->session()->put('guest_id', 'guest_' . Str::random(16));
            $request->session()->put('guest_name', 'Guest ' . random_int(1000, 9999));
        }

        return $next($request);
    }
}
