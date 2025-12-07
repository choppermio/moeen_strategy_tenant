<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organization;
use App\Models\User;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create default organization
        $organization = Organization::firstOrCreate(
            ['slug' => 'default'],
            [
                'name' => 'المنظمة الرئيسية',
                'slug' => 'default',
                'description' => 'المنظمة الافتراضية للنظام',
                'is_active' => true,
            ]
        );

        $this->command->info('Default organization created: ' . $organization->name);

        // Assign all existing users to this organization
        $users = User::all();
        $assignedCount = 0;

        foreach ($users as $user) {
            // Check if user is already assigned to this organization
            if (!$organization->users()->where('user_id', $user->id)->exists()) {
                // First user becomes admin, rest are members
                $role = $assignedCount === 0 ? 'admin' : 'member';
                
                $organization->users()->attach($user->id, [
                    'role' => $role,
                    'is_default' => true,
                ]);

                // Set current organization for user
                $user->current_organization_id = $organization->id;
                $user->save();

                $assignedCount++;
            }
        }

        $this->command->info("Assigned {$assignedCount} users to the default organization.");

        // Update existing data with organization_id
        $this->updateExistingDataWithOrganization($organization->id);
    }

    /**
     * Update all existing tenant data with the organization ID
     */
    protected function updateExistingDataWithOrganization($organizationId)
    {
        $tables = [
            'employee_positions',
            'employee_position_relations',
            'hadafstrategies',
            'mubadaras',
            'tasks',
            'subtasks',
            'tickets',
            'ticket_transitions',
            'todos',
            'moashers',
            'moashermkmfs',
            'moasheradastrategies',
            'messages',
            'images',
            'task_user_assignments',
        ];

        foreach ($tables as $table) {
            try {
                $updated = \DB::table($table)
                    ->whereNull('organization_id')
                    ->update(['organization_id' => $organizationId]);
                
                if ($updated > 0) {
                    $this->command->info("Updated {$updated} records in {$table} with organization_id.");
                }
            } catch (\Exception $e) {
                $this->command->warn("Could not update {$table}: " . $e->getMessage());
            }
        }
    }
}
