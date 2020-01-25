<?php

use App\Eloquents\Contract;
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
        if (is_null(Contract::find(1))) {
            Contract::create([
                'id' => 1,
                'name' => '管理グループ',
            ]);
        }

        if (is_null(User::whereEmail('admin@example.com')->first())) {
            User::create([
                'contract_id' => 1,
                'name' => 'デフォルト管理者',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'per_admin' => true,
            ]);
        }
    }
}
