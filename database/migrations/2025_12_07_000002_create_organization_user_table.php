<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organization_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role')->default('member'); // admin, manager, member
            $table->boolean('is_default')->default(false); // default org for user
            $table->timestamps();

            $table->unique(['organization_id', 'user_id']);
        });

        // Add current_organization_id to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('current_organization_id')->nullable()->after('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('current_organization_id');
        });

        Schema::dropIfExists('organization_user');
    }
}
