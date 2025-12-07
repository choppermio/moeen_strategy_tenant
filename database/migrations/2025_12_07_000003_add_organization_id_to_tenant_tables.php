<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrganizationIdToTenantTables extends Migration
{
    /**
     * The tables that need organization_id for multi-tenancy
     */
    protected $tables = [
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

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'organization_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->foreignId('organization_id')->nullable()->after('id');
                    $table->index('organization_id');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'organization_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropIndex(['organization_id']);
                    $table->dropColumn('organization_id');
                });
            }
        }
    }
}
