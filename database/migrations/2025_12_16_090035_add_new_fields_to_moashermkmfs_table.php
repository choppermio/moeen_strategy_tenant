<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFieldsToMoashermkmfsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('moashermkmfs', function (Blueprint $table) {
            $table->decimal('reached', 10, 2)->nullable();
            $table->decimal('target', 10, 2)->nullable();
            $table->string('calculation_type')->nullable();
            $table->string('the_vari')->nullable();
            $table->decimal('weight', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('moashermkmfs', function (Blueprint $table) {
            $table->dropColumn(['reached', 'target', 'calculation_type', 'the_vari', 'weight']);
        });
    }
}
