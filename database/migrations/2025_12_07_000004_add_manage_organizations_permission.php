<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddManageOrganizationsPermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add manage_organizations permission
        DB::table('permissions')->insertOrIgnore([
            'name' => 'manage_organizations',
            'display_name' => 'إدارة المنظمات',
            'description' => 'إضافة وتعديل وحذف المنظمات وإدارة أعضائها',
            'route' => 'organizations.*',
            'group' => 'organizations',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('permissions')->where('name', 'manage_organizations')->delete();
    }
}
