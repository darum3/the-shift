<?php

use App\Lib\Migrations\Util;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllowFunctions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allow_functions', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('許可機能ID');
            $table->unsignedBigInteger('contract_id')->comment('契約ID');
            $table->foreign('contract_id')->on('contracts')->references('id')->onDelete('cascade');

            $table->boolean('per_shift')->default(false)->comment('シフト機能');
            $table->boolean('per_time_record')->default(false)->comment('タイムカード機能');
            $table->boolean('per_payment')->default(false)->comment('給与計算機能');

            Util::CommonColumn($table, false);
        });
        DB::statement("ALTER TABLE allow_functions COMMENT '許可機能';");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('allow_functions');
    }
}
