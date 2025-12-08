<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\ResponseRedirect)  $next
     * @param  string  $permission
     * @return \Illuminate\Http\Response|\Illuminate\Http\ResponseRedirect
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $position = current_user_position();
        
        // Check if user is system admin (based on ADMIN_ID env)
        if ($position && is_admin($position->id)) {
            return $next($request);
        }

        // Check if user has 'admin' role in any organization
        $hasAdminRole = $user->organizations()
            ->wherePivot('role', 'admin')
            ->exists();
        
        if ($hasAdminRole) {
            return $next($request);
        }

        // For regular users, check their position and permissions
        if (!$position) {
            abort(403, 'ليس لديك صلاحية الوصول إلى هذه الصفحة');
        }

        // Check if user has the required permission
        if (!$position->hasPermission($permission)) {
            abort(403, 'ليس لديك صلاحية الوصول إلى هذه الصفحة');
        }

        return $next($request);
    }
}
