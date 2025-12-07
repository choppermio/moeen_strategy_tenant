<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureOrganizationAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role = null)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $organizationId = session('current_organization_id') ?? $user->current_organization_id;

        // Check if user has an organization set
        if (!$organizationId) {
            if ($user->organizations()->count() === 0) {
                // User has no organizations assigned
                return redirect()->route('no-organization')
                    ->with('error', 'لم يتم تعيينك لأي منظمة. يرجى التواصل مع المسؤول.');
            }
            
            // Redirect to organization selection if user has multiple orgs
            return redirect()->route('organization.select');
        }

        // Check if user belongs to the organization
        if (!$user->belongsToOrganization($organizationId)) {
            session()->forget('current_organization_id');
            return redirect()->route('organization.select')
                ->with('error', 'ليس لديك صلاحية الوصول لهذه المنظمة.');
        }

        // If a specific role is required, check it
        if ($role) {
            $userRole = $user->roleInOrganization($organizationId);
            
            $roleHierarchy = ['member' => 1, 'manager' => 2, 'admin' => 3];
            
            if (!isset($roleHierarchy[$userRole]) || 
                $roleHierarchy[$userRole] < $roleHierarchy[$role]) {
                abort(403, 'ليس لديك الصلاحيات الكافية لهذا الإجراء.');
            }
        }

        return $next($request);
    }
}
