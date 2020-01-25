<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Lib\Migrations\Util;
use Illuminate\Support\Facades\DB;

class CreateContract extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('契約ID');
            $table->string('name', 50)->unique()->comment('契約名');
            Util::CommonColumn($table, true);
        });
        DB::statement("ALTER TABLE contracts COMMENT '契約'");
        DB::statement("ALTER TABLE contracts AUTO_INCREMENT = 5000");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contracts');
    }
}
