<?php

use Illuminate\Database\Seeder;
use App\User;

class CreateSystemAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (is_null(User::whereEmail('admin@example.com')->first())) {
            User::create([
                'name' => 'デフォルト管理者',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'flg_system_admin' => true,
            ]);
        }
    }
}
