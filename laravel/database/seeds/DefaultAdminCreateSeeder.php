<?php

use App\User;
use Illuminate\Database\Seeder;

class DefaultAdminCreateSeeder extends Seeder
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
                'per_admin' => true,
            ]);
        }
    }
}
