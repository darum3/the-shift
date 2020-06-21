<?php

use App\Lib\Migrations\Util;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateInputedDates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inputed_dates', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('希望入力済みID');
            $table->unsignedBigInteger('group_id')->comment('グループID');
            $table->foreign('group_id')->on('groups')->references('id')->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->comment('ユーザID');
            $table->foreign('user_id')->on('users')->references('id')->onDelete('cascade');
            $table->date('date_target')->comment('日付');

            Util::CommonColumn($table);
        });
        DB::statement("ALTER TABLE inputed_dates COMMENT '希望入力済み日付'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inputed_dates');
    }
}
