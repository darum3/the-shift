<?php
namespace App\Lib\Migrations;

use Illuminate\Database\Schema\Blueprint;

class Util
{
    public static function CommonColumn(Blueprint $table, bool $isMaster = false, string $validName = '有効フラグ')
    {
        if ($isMaster) {
            $table->boolean('valid')->default(true)->comment($validName);
        }
        $table->integer('version', false, true)->default(1)->comment('バージョン');
        $table->timestamps();
        $table->softDeletes();
        $table->string('sysinfo', 64);
    }
}
