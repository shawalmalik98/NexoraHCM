<?php

namespace App\Http\Middleware;

use App\Helpers\CustomHelper;
use Closure;
use Illuminate\Http\Request;

/**
 * Guard for routes outside the module permission group:
 *   ->middleware('access:module,/employee-location')   the employee needs that module (own permission or role)
 *   ->middleware('access:role,hr|finance')             the employee needs one of these roles (Super Admin always passes)
 */
class AccessCheck
{
    public function handle(Request $request, Closure $next, $type, $value)
    {
        $employee_id = session('user_session');

        $allowed = false;
        if (!empty($employee_id)) {
            if ($type === 'module') {
                $allowed = CustomHelper::can_access($value, $employee_id);
            } elseif ($type === 'role') {
                foreach (explode('|', $value) as $role) {
                    if (CustomHelper::has_role($employee_id, $role)) {
                        $allowed = true;
                        break;
                    }
                }
            }
        }

        if (!$allowed) {
            if ($request->expectsJson() || $request->ajax() || $request->isMethod('post')) {
                return response()->json(['status' => 'FALSE', 'msg' => 'You are not allowed to do this.'], 403);
            }
            return response()->view('errors.403', [], 403);
        }

        return $next($request);
    }
}
