<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PresenceDemoController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = null;
        
        if (auth()->check()) {
            $currentUser = [
                'id' => auth()->id(),
                'name' => auth()->user()->name,
                'type' => 'authenticated',
            ];
        } else {
            $currentUser = [
                'id' => $request->session()->get('guest_id'),
                'name' => $request->session()->get('guest_name'),
                'type' => 'guest',
            ];
        }
        
        return view('presence-demo', ['currentUser' => $currentUser]);
    }
}