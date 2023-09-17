<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ManagerSessionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Assuming the employee session key is 'employee'
        if ($request->session()->get('employee')->job !='Manager') {
            return redirect()->route('login-page');
        }
        return $next($request);
    }
}
