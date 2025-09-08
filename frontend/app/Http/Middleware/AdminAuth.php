<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek session login
        if (session('admin_logged_in')) {
            return $next($request);
        }

        // Cek localStorage remember me (akan dicek di frontend)
        // Middleware ini hanya redirect ke login, frontend akan handle auto login

        return redirect('/login');
    }
}
