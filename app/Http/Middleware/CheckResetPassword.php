<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckResetPassword
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if (empty($user) || empty($user->employee)) {
            return $next($request);
        }

        if ($user->employee->force_reset_password) {
            return redirect()->route('change-password');
        }

        return $next($request);
    }
}
