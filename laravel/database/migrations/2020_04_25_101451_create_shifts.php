<?php

use App\Lib\Migrations\Util;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateShifts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('シフトID');
            $table->unsignedBigInteger('group_id')->comment('グループID');
            $table->foreign('group_id')->on('groups')->references('id')->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->comment('ユーザID');
            $table->foreign('user_id')->on('users')->references('id')->onDelete('cascade');
            $table->unsignedBigInteger('work_type_id')->comment('職種ID');
            $table->foreign('work_type_id')->on('work_types')->references('id')->onDelete('cascade');
            $table->dateTime('start_datetime')->comment('出勤');
            $table->dateTime('end_datetime')->comment('退勤');

            Util::CommonColumn($table, true);
        });
        DB::statement("ALTER TABLE shifts COMMENT 'シフト'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shifts');
    }
}
