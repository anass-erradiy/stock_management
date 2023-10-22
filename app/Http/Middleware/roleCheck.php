<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class roleCheck
{
   
    public function handle(Request $request, Closure $next,$role)
    {
        if(Auth::user()->hasRole($role))
            return $next($request);
        $errorMessages = [
            'seller' => 'You do not have permission to seller actions !',
            'buyer' => 'You do not have permission to buyer actions !',
            'admin' => 'You do not have permission to admin actions !',
        ];
        return response()->json([
            'error' => $errorMessages[$role]
        ],403) ;
}
}
