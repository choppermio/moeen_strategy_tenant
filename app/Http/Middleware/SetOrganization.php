<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetOrganization
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
        if (auth()->check()) {
            // Get fresh user data from database
            $user = \App\Models\User::find(auth()->id());
            
            // Get organization ID from database (primary source of truth)
            $organizationId = $user->current_organization_id;

            // If no organization is set in database, try to get the default one
            if (!$organizationId) {
                $defaultOrg = $user->defaultOrganization();
                if ($defaultOrg) {
                    $organizationId = $defaultOrg->id;
                } elseif ($user->organizations()->count() > 0) {
                    $organizationId = $user->organizations()->first()->id;
                } elseif ($user->isSystemAdmin()) {
                    // For admins without assigned orgs, get first active org
                    $firstOrg = \App\Models\Organization::where('is_active', true)->first();
                    $organizationId = $firstOrg ? $firstOrg->id : null;
                }
                
                // Save only if we found an organization and user didn't have one
                if ($organizationId) {
                    \DB::table('users')->where('id', $user->id)->update([
                        'current_organization_id' => $organizationId
                    ]);
                }
            }
            
            // Set session to match database
            session(['current_organization_id' => $organizationId]);

            // Share current organization with all views
            if ($organizationId) {
                $currentOrganization = \App\Models\Organization::find($organizationId);
                view()->share('currentOrganization', $currentOrganization);
                view()->share('userOrganizations', $user->accessibleOrganizations());
            }
        }

        return $next($request);
    }
}
