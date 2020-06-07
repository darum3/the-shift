<?php

namespace Tests\Feature\GroupManage\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;
use App\Eloquents\Contract;
use App\Eloquents\Group;
use App\Eloquents\UserGroup;
use App\Eloquents\WorkType;

class AddTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->contract = factory(Contract::class)->create();
        $this->group = factory(Group::class)->create([
            'contract_id' => $this->contract->id,
        ]);
        factory(UserGroup::class)->create([
            'group_id' => $this->group->id,
            'user_id' => $this->user->id,
            'flg_admin' => true,  // グループの管理者（店長）
        ]);

        $this->workType = factory(WorkType::class)->create([
            'contract_id' => $this->contract->id,
            'code' => 'MAKE',
        ]);

        $this->workUsers = factory(User::class, 2)->create();
        foreach($this->workUsers as $worker) {
            factory(UserGroup::class)->create([
                'group_id' => $this->group->id,
                'user_id' => $worker->id,
            ]);
        }
    }

    public function test追加画面表示()
    {
        $this->ActingAs($this->user)->withSession([
            'contract_id' => $this->contract->id,
            'group_id' => $this->group->id,
        ])->get('/g-manage/user/add/input')->assertOk()
        ->assertViewIs('g-mng.user.add.input')
        ->assertViewHasAll([
            'group' => Group::find($this->group->id),
            'fields' => [
                ['label' => '名前', 'width' => 4, 'name' => 'user_name'],
                ['label' => 'メールアドレス', 'width' => 4, 'name' => 'email'],
                ['label' => 'パスワード', 'type' => 'password', 'width' => 4, 'name' => 'password'],
            ], 'action' => url('/g-manage/user/add/confirm'),
        ]);
    }
}
