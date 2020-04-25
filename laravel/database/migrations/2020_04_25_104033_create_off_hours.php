<?php

use App\Lib\Migrations\Util;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateOffHours extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('off_hours', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('休憩時間ID');
            $table->unsignedBigInteger('shift_id')->comment('シフトID');
            $table->foreign('shift_id')->on('shifts')->references('id')->onDelete('cascade');
            $table->datetime('off_start_datetime')->comment('休憩開始日時');
            $table->datetime('off_end_datetime')->comment('休憩終了日時');

            Util::CommonColumn($table, true);
        });
        DB::statement("ALTER TABLE off_hours COMMENT '休憩時間'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('off_hours');
    }
}
