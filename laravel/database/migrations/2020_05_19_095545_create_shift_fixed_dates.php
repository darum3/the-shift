<?php

use App\Lib\Migrations\Util;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateShiftFixedDates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_fixed_dates', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('シフト確定日ID');
            $table->unsignedBigInteger('group_id')->comment('グループID');
            $table->foreign('group_id')->on('groups')->references('id')->onDelete('cascade');
            $table->date('date_fixed')->comment('確定日');

            $table->unique(['group_id', 'date_fixed'], 'UQ_SHOFT_FIXED_DATES_01');

            Util::CommonColumn($table);
        });
        DB::statement("ALTER TABLE shift_fixed_dates COMMENT 'シフト確定日'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shift_fixed_dates');
    }
}
