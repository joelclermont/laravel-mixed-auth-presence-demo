<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('mixed-auth-presence-demo', function ($user) {
    if (! $user) {
        return false;
    }
    
    $isGuest = $user->getAttribute('is_guest') ?? false;
    
    return [
        'id' => $user->id,
        'name' => $user->name,
        'type' => $isGuest ? 'guest' : 'authenticated',
    ];
}, ['guards' => ['broadcast-guest']]);
