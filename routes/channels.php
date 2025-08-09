<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('mixed-auth-presence-demo', function ($user = null) {
    if ($user) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'type' => 'authenticated',
        ];
    }
    
    $guestId = request()->session()->get('guest_id');
    $guestName = request()->session()->get('guest_name');
    
    if ($guestId) {
        return [
            'id' => $guestId,
            'name' => $guestName,
            'type' => 'guest',
        ];
    }
    
    return false;
});
