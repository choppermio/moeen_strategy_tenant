<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Permission;
use App\Models\EmployeePosition;

class AssignOrgPermission extends Command
{
    protected $signature = 'org:assign-permission {permission_name?} {position_ids?}';
    protected $description = 'Assign a permission to admin positions';

    public function handle()
    {
        $permissionName = $this->argument('permission_name') ?: 'manage_organizations';
        $positionIds = $this->argument('position_ids') 
            ? explode(',', $this->argument('position_ids'))
            : [4, 44]; // Default admin IDs from .env

        $permission = Permission::where('name', $permissionName)->first();
        
        if (!$permission) {
            $this->error("Permission '{$permissionName}' not found!");
            $this->info("Available permissions:");
            Permission::all()->each(function($p) {
                $this->line(" - {$p->name}");
            });
            return 1;
        }

        foreach ($positionIds as $posId) {
            $position = EmployeePosition::find(trim($posId));
            
            if (!$position) {
                $this->warn("Position ID {$posId} not found, skipping...");
                continue;
            }

            if ($position->permissions()->where('permission_id', $permission->id)->exists()) {
                $this->info("Position {$position->name} (ID: {$posId}) already has the permission.");
            } else {
                $position->permissions()->attach($permission->id);
                $this->info("Assigned {$permissionName} to {$position->name} (ID: {$posId})");
            }
        }

        $this->newLine();
        $this->info('Done!');
        
        return 0;
    }
}
