<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCalculationVariableToMoashermkmfsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('moashermkmfs', function (Blueprint $table) {
            $table->string('calculation_variable')->nullable()->after('the_vari');
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
            $table->dropColumn('calculation_variable');
        });
    }
}
