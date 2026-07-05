<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Support\Facades\Auth;

class CheckStatus

{

    /**

     * Handle an incoming request.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  \Closure  $next

     * @return mixed

     */

    public function handle($request, Closure $next)

    {

        $response = $next($request);

        //If the status is not approved redirect to login 

        if(Auth::check() && Auth::user()->status != '1'){
          Auth::logout();
          $request->session()->flash('alert-danger', 'Your Account is not activated yet.');
          $message="Your Account is not activated yet.";
          return redirect('/')->with('flash_warning', $message);
        }else if(!Auth::check()){
          $message="Session Expired or Your Email or Password not matched yet.";
          return redirect('/')->with('flash_warning', $message);
        }

        return $response;

    }

}