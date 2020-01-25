<?php
namespace App\Lib\Migrations;

use Illuminate\Database\Schema\Blueprint;

class Util
{
    public static function CommonColumn(Blueprint $table, bool $isMaster = false)
    {
        if ($isMaster) {
            $table->boolean('valid')->default(true);
        }
        $table->integer('version', false, true)->default(1)->comment('バージョン');
        $table->timestamps();
        $table->softDeletes();
        $table->string('sysinfo', 64);
    }
}
