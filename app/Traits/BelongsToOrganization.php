<?php

namespace App\Traits;

use App\Models\Organization;
use App\Scopes\OrganizationScope;

trait BelongsToOrganization
{
    /**
     * Boot the trait
     */
    protected static function bootBelongsToOrganization()
    {
        // Apply global scope for filtering by organization
        static::addGlobalScope(new OrganizationScope);

        // Auto-set organization_id when creating new records
        static::creating(function ($model) {
            if (empty($model->organization_id)) {
                $model->organization_id = static::getCurrentOrganizationId();
            }
        });
    }

    /**
     * Get the current organization ID
     *
     * @return int|null
     */
    protected static function getCurrentOrganizationId()
    {
        // First check session
        if (session()->has('current_organization_id')) {
            return session('current_organization_id');
        }

        // Then check authenticated user
        if (auth()->check() && auth()->user()->current_organization_id) {
            return auth()->user()->current_organization_id;
        }

        return null;
    }

    /**
     * Get the organization that this model belongs to
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Scope to query without organization filter
     */
    public function scopeWithoutOrganization($query)
    {
        return $query->withoutGlobalScope(OrganizationScope::class);
    }

    /**
     * Scope to query for a specific organization
     */
    public function scopeForOrganization($query, $organizationId)
    {
        return $query->withoutGlobalScope(OrganizationScope::class)
            ->where('organization_id', $organizationId);
    }
}
