<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Seed permissions first
        $this->call(PermissionsSeeder::class);
        
        // Seed organizations and assign users
        $this->call(OrganizationSeeder::class);
    }
}
