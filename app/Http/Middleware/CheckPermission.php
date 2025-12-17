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
        
        // Check if user has 'admin' role in ANY organization first
        // This allows admins to access everything even without an employee position
        $hasAdminRole = $user->organizations()
            ->wherePivot('role', 'admin')
            ->exists();
        
        if ($hasAdminRole) {
            return $next($request);
        }

        // Check if user has 'admin' role in current organization
        if ($user->isOrganizationAdmin()) {
            return $next($request);
        }
        
        // Get position after admin checks
        $position = current_user_position();
        
        // Check if user is system admin (based on ADMIN_ID env)
        if ($position && is_admin($position->id)) {
            return $next($request);
        }

        // For regular users, check their position and permissions
        if (!$position) {
            abort(403, 'ليس لديك صلاحية الوصول إلى هذه الصفحة - لا يوجد منصب وظيفي');
        }

        // Check if user has the required permission
        if (!$position->hasPermission($permission)) {
            abort(403, 'ليس لديك صلاحية الوصول إلى هذه الصفحة');
        }

        return $next($request);
    }
}
