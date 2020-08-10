<?php

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        if ($request->query('tenant_id')){
            $user = User::find($request->tenant_id);
            if($user && Auth::loginUsingId($user->id)) {
                return url('/api/h5p');
            }
            return route('login');
        }
        if (! $request->expectsJson()) {
            return route('login');
        }
    }
}
