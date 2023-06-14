<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
   /**
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @return mixed
    */
   public function handle(Request $request, Closure $next)
   {
      // if(Auth::user() && Auth::user()->roles == 'ADMIN') {
      //   return $next($request);
      // } else {
      //   return redirect('/');
      // }
      if (Auth::user() && Auth::user()->role == 30) {
         return $next($request);
      } else {
         Auth::logout();
         return abort(403);
         // return response()->view("errors.index");
      }
   }
}
