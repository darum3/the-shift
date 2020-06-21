<?php

use App\Lib\Migrations\Util;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateDesiredShifts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('desired_shifts', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('希望シフトID');
            $table->unsignedBigInteger('group_id')->comment('グループID');
            $table->foreign('group_id')->on('groups')->references('id')->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->comment('ユーザID');
            $table->foreign('user_id')->on('users')->references('id')->onDelete('cascade');
            $table->unsignedBigInteger('work_type_id')->nullable()->comment('職種ID');
            $table->foreign('work_type_id')->on('work_types')->references('id')->onDelete('cascade');
            $table->date('date_target')->comment('日付');
            $table->time('time_start')->comment('開始時刻');
            $table->time('time_end')->comment('終了時刻');

            Util::CommonColumn($table);
        });
        DB::statement("ALTER TABLE desired_shifts COMMENT '希望シフト'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('desired_shifts');
    }
}
