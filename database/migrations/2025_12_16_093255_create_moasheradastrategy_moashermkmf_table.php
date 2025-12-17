<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoasheradastrategyMoashermkmfTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('moasheradastrategy_moashermkmf', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('moasheradastrategy_id');
            $table->unsignedBigInteger('moashermkmf_id');
            $table->timestamps();
            
            $table->foreign('moasheradastrategy_id')->references('id')->on('moasheradastrategies')->onDelete('cascade');
            $table->foreign('moashermkmf_id')->references('id')->on('moashermkmfs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('moasheradastrategy_moashermkmf');
    }
}
