<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\EmployeePosition;
use App\Models\Organization;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create {--email=admin@moeen-sa.com} {--password=123456789} {--name=مدير النظام}';
    protected $description = 'Create an admin user with an employee position';

    public function handle()
    {
        $email = $this->option('email');
        $password = $this->option('password');
        $name = $this->option('name');

        // Check if user already exists
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            $this->warn("User with email {$email} already exists (ID: {$existingUser->id})");
            
            // Update password
            $existingUser->password = bcrypt($password);
            $existingUser->save();
            $this->info("Password updated to: {$password}");
            
            // Check if has position
            $position = EmployeePosition::where('user_id', $existingUser->id)->first();
            if ($position) {
                $this->info("User already has position: {$position->name} (ID: {$position->id})");
            }
            
            return 0;
        }

        // Create user
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password),
        ]);

        $this->info("User created successfully!");
        $this->info("  ID: {$user->id}");
        $this->info("  Name: {$user->name}");
        $this->info("  Email: {$user->email}");

        // Create employee position for admin
        $adminIds = array_filter(array_map('trim', explode(',', env('ADMIN_ID', ''))));
        $firstAdminId = !empty($adminIds) ? $adminIds[0] : null;

        // Check if we should link to existing admin position or create new
        if ($firstAdminId) {
            $existingPosition = EmployeePosition::find($firstAdminId);
            if ($existingPosition && !$existingPosition->user_id) {
                // Link user to existing admin position
                $existingPosition->user_id = $user->id;
                $existingPosition->save();
                $this->info("Linked to existing admin position: {$existingPosition->name} (ID: {$existingPosition->id})");
            } else {
                // Create new position
                $position = $this->createPosition($user);
            }
        } else {
            // Create new position
            $position = $this->createPosition($user);
        }

        // Assign to default organization
        $defaultOrg = Organization::first();
        if ($defaultOrg) {
            $user->organizations()->attach($defaultOrg->id, [
                'role' => 'admin',
                'is_default' => true,
            ]);
            $user->current_organization_id = $defaultOrg->id;
            $user->save();
            $this->info("Assigned to organization: {$defaultOrg->name} as admin");
        }

        $this->newLine();
        $this->info("=== Login Credentials ===");
        $this->info("Email: {$email}");
        $this->info("Password: {$password}");
        $this->newLine();
        $this->warn("Note: Add this position ID to ADMIN_ID in .env if needed");

        return 0;
    }

    private function createPosition($user)
    {
        // Use direct insert to avoid mass assignment issues
        $positionId = \DB::table('employee_positions')->insertGetId([
            'name' => 'مدير النظام - ' . $user->name,
            'user_id' => $user->id,
            'organization_id' => Organization::first()?->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $position = EmployeePosition::find($positionId);
        $this->info("Created employee position: {$position->name} (ID: {$position->id})");
        return $position;
    }
}
