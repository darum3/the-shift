<?php

use App\Lib\Migrations\Util;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUserGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_group', function (Blueprint $table) {
            $table->unsignedBigInteger('group_id')->comment('グループID');
            $table->foreign('group_id')->on('groups')->references('id')->onDelete('cascade');

            $table->unsignedBigInteger('user_id')->comment('ユーザID');
            $table->foreign('user_id')->on('users')->references('id')->onDelete('cascade');

            $table->boolean('flg_admin')->default(false)->comment('グループ管理者フラグ');

            Util::CommonColumn($table);

            $table->unique(['group_id', 'user_id'], 'UQ_GROUP_USER_REL_01');
        });
        DB::statement("ALTER TABLE user_group COMMENT 'グループ_ユーザ設定'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_group');
    }
}
