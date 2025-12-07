<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the users that belong to this organization
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'organization_user')
            ->withPivot('role', 'is_default')
            ->withTimestamps();
    }

    /**
     * Get admin users of this organization
     */
    public function admins()
    {
        return $this->users()->wherePivot('role', 'admin');
    }

    /**
     * Get managers of this organization
     */
    public function managers()
    {
        return $this->users()->wherePivot('role', 'manager');
    }

    /**
     * Get employee positions for this organization
     */
    public function employeePositions()
    {
        return $this->hasMany(EmployeePosition::class);
    }

    /**
     * Get hadafstrategies for this organization
     */
    public function hadafstrategies()
    {
        return $this->hasMany(Hadafstrategy::class);
    }

    /**
     * Get mubadaras for this organization
     */
    public function mubadaras()
    {
        return $this->hasMany(Mubadara::class);
    }

    /**
     * Get tasks for this organization
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get subtasks for this organization
     */
    public function subtasks()
    {
        return $this->hasMany(Subtask::class);
    }

    /**
     * Get tickets for this organization
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Get moashers for this organization
     */
    public function moashers()
    {
        return $this->hasMany(Moasher::class);
    }

    /**
     * Scope to only include active organizations
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
