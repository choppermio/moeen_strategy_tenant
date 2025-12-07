<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class OrganizationScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $organizationId = $this->getCurrentOrganizationId();

        if ($organizationId) {
            $builder->where($model->getTable() . '.organization_id', $organizationId);
        }
    }

    /**
     * Get the current organization ID from session or user
     *
     * @return int|null
     */
    protected function getCurrentOrganizationId()
    {
        // First check session
        if (session()->has('current_organization_id')) {
            return session('current_organization_id');
        }

        // Then check authenticated user
        if (auth()->check() && auth()->user()->current_organization_id) {
            return auth()->user()->current_organization_id;
        }

        // Return null if no organization is set
        return null;
    }
}
