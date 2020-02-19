<?php

use App\Lib\Migrations\Util;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateWorkTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_types', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('職種ID');
            $table->unsignedBigInteger('contract_id')->comment('契約ID');
            $table->foreign('contract_id')->on('contracts')->references('id')->onDelete('cascade');

            $table->string('code', 10)->comment('コード');
            $table->string('name', 50)->comment('名称');

            Util::CommonColumn($table, true, '利用中フラグ');

            $table->unique(['contract_id', 'code'], 'UQ_WORK_TYPES_CODE_01');
        });
        DB::statement("ALTER TABLE work_types COMMENT '職種マスタ'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('work_types');
    }
}
