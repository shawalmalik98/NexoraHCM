<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\CustomHelper;

class PermissionChecker
{
    /**
     * Only lets the logged in employee open a module (first url segment) granted to them,
     * directly or through one of their roles.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(!empty(session('user_session'))){
            $route = '/' . ($request->segment(1) ?? '');

            if(!CustomHelper::can_access($route)){
                return response()->view('errors.403', [], 403);
            }
        }
        return $next($request);
    }
}
