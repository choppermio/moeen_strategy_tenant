<?php

namespace App\Models;

use App\Models\EmployeePosition;
use App\Models\Organization;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'current_organization_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function position() {
        return $this->belongsTo(EmployeePosition::class, 'user_id', 'id');
    }

    /**
     * Get all organizations the user belongs to
     * For admins, this returns all active organizations
     */
    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'organization_user')
            ->withPivot('role', 'is_default')
            ->withTimestamps();
    }

    /**
     * Get all accessible organizations for the user
     * Users with 'admin' role in any organization get all organizations
     * Regular users get only assigned ones
     */
    public function accessibleOrganizations()
    {
        // Check if user is system admin (based on ADMIN_ID in .env)
        if ($this->isSystemAdmin()) {
            return Organization::where('is_active', true)->get();
        }
        
        // Check if user has 'admin' role in any organization
        $hasAdminRole = $this->organizations()
            ->wherePivot('role', 'admin')
            ->exists();
        
        if ($hasAdminRole) {
            return Organization::where('is_active', true)->get();
        }
        
        // Regular users get only their assigned organizations
        return $this->organizations()->where('is_active', true)->get();
    }

    /**
     * Get the current organization
     */
    public function currentOrganization()
    {
        return $this->belongsTo(Organization::class, 'current_organization_id');
    }

    /**
     * Get the default organization for the user
     */
    public function defaultOrganization()
    {
        return $this->organizations()->wherePivot('is_default', true)->first();
    }

    /**
     * Switch to a different organization
     *
     * @param int|Organization $organization
     * @return bool
     */
    public function switchOrganization($organization)
    {
        $organizationId = $organization instanceof Organization 
            ? $organization->id 
            : $organization;

        // Check if user belongs to this organization
        if (!$this->belongsToOrganization($organizationId)) {
            return false;
        }

        $this->current_organization_id = $organizationId;
        $this->save();

        // Also set in session for immediate use
        session(['current_organization_id' => $organizationId]);

        return true;
    }

    /**
     * Check if user belongs to an organization
     *
     * @param int|Organization $organization
     * @return bool
     */
    public function belongsToOrganization($organization)
    {
        // System admins (based on ADMIN_ID) have access to all organizations
        if ($this->isSystemAdmin()) {
            return true;
        }
        
        // Users with 'admin' role in any organization have access to all organizations
        $hasAdminRole = $this->organizations()
            ->wherePivot('role', 'admin')
            ->exists();
        
        if ($hasAdminRole) {
            return true;
        }
        
        $organizationId = $organization instanceof Organization 
            ? $organization->id 
            : $organization;

        return $this->organizations()->where('organizations.id', $organizationId)->exists();
    }

    /**
     * Check if user is a system admin (based on ADMIN_ID env)
     *
     * @return bool
     */
    public function isSystemAdmin()
    {
        $position = \App\Models\EmployeePosition::where('user_id', $this->id)->first();
        if (!$position) {
            return false;
        }
        
        $adminIds = array_filter(array_map('trim', explode(',', env('ADMIN_ID', ''))));
        return in_array((string)$position->id, $adminIds);
    }

    /**
     * Get user's role in a specific organization
     *
     * @param int|Organization $organization
     * @return string|null
     */
    public function roleInOrganization($organization)
    {
        $organizationId = $organization instanceof Organization 
            ? $organization->id 
            : $organization;

        $org = $this->organizations()->where('organizations.id', $organizationId)->first();
        
        // If user is a system admin but not explicitly assigned to this org, they have admin role
        if (!$org && $this->isSystemAdmin()) {
            return 'admin';
        }
        
        return $org ? $org->pivot->role : null;
    }

    /**
     * Check if user is admin in current organization
     *
     * @return bool
     */
    public function isOrganizationAdmin()
    {
        if (!$this->current_organization_id) {
            return false;
        }

        return $this->roleInOrganization($this->current_organization_id) === 'admin';
    }

    /**
     * Check if user is manager in current organization
     *
     * @return bool
     */
    public function isOrganizationManager()
    {
        if (!$this->current_organization_id) {
            return false;
        }

        $role = $this->roleInOrganization($this->current_organization_id);
        return in_array($role, ['admin', 'manager']);
    }

    /**
     * Set the user's default organization
     *
     * @param int|Organization $organization
     * @return bool
     */
    public function setDefaultOrganization($organization)
    {
        $organizationId = $organization instanceof Organization 
            ? $organization->id 
            : $organization;

        // First, remove default from all other organizations
        $this->organizations()->updateExistingPivot(
            $this->organizations()->pluck('organizations.id')->toArray(),
            ['is_default' => false]
        );

        // Set new default
        if ($this->belongsToOrganization($organizationId)) {
            $this->organizations()->updateExistingPivot($organizationId, ['is_default' => true]);
            return true;
        }

        return false;
    }

    /**
     * Get current organization ID (from session or user record)
     *
     * @return int|null
     */
    public function getCurrentOrganizationId()
    {
        // Check session first
        if (session()->has('current_organization_id')) {
            return session('current_organization_id');
        }

        // Then check user's saved organization
        if ($this->current_organization_id) {
            return $this->current_organization_id;
        }

        // Finally, get default organization
        $defaultOrg = $this->defaultOrganization();
        return $defaultOrg ? $defaultOrg->id : null;
    }
}
