<?php

namespace App\Providers;

use App\Models\User;
use App\Rules\Phone;
use App\Rules\PhoneNew;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\InvokableValidationRule;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app['validator']->extend('phone', Phone::class . '@passes');

        $this->app['validator']->extend('phone_new', fn($attribute, $value, $parameters, $validator) => InvokableValidationRule::make(new PhoneNew())->setValidator($validator)->passes($attribute, $value));
        
        Auth::viaRequest('broadcast-guest', function ($request) {
            if ($user = Auth::guard('web')->user()) {
                return $user;
            }
            
            $guestId = $request->session()->get('guest_id');
            $guestName = $request->session()->get('guest_name');
            
            if ($guestId && $guestName) {
                $guestUser = new User();
                $guestUser->id = $guestId;
                $guestUser->name = $guestName;
                $guestUser->email = 'guest_' . $guestId . '@guest.local';
                $guestUser->setAttribute('is_guest', true);
                
                return $guestUser;
            }
            
            return null;
        });
    }
}
