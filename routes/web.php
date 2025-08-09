<?php

declare(strict_types=1);

use App\Http\Controllers\PresenceDemoController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/',  fn() => auth()->check() ? 'Logged in' : 'Logged out');

Route::get('/presence-demo', [PresenceDemoController::class, 'index']);

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

