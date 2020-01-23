<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class UserGropusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::table('users', function(Blueprint $table) {
            foreach(config('users.groups') as $group) {
                if (! (Schema::hasColumn('users', $group['column']))) {
                    $table->boolean($group['column'])->comment($group['desc'])->default(false);
                }
            }
        });
    }
}
