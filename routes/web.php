<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/',  fn() => auth()->check() ? 'Logged in' : 'Logged out');

Route::get('/presence-demo', function () {
    return view('presence-demo', ['user' => auth('broadcast-guest')->user()]);
});

// Some dummy routes to easily simulate logging in and out for the demo
Route::get('/login', function () {
    $user = User::firstOrCreate(
        ['email' => 'demo@example.com'],
        [
            'name' => 'Demo User',
            'password' => bcrypt('password'),
        ]
    );

    Auth::login($user);

    return redirect('/presence-demo');
});

Route::post('/logout', function () {
    Auth::logout();

    return redirect('/presence-demo');
});

