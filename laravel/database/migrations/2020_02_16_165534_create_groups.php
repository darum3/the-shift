<?php

use App\Lib\Migrations\Util;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('グループID');
            $table->unsignedBigInteger('contract_id')->comment('契約ID');
            $table->foreign('contract_id')->on('contracts')->references('id')->onDelete('cascade');

            $table->string('name', 50)->comment('グループ名');
            $table->boolean('flg_admin')->default(false)->comment('管理グループフラグ');

            Util::CommonColumn($table, true);
        });
        DB::statement("ALTER TABLE groups COMMENT 'グループ(組織)'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('groups');
    }
}
