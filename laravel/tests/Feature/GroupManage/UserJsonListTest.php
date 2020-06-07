<?php

namespace Tests\Feature\GroupManage;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\TestResponse;
use App\User;
use App\Eloquents\Contract;
use App\Eloquents\Group;
use App\Eloquents\UserGroup;
use App\Eloquents\WorkType;

class UserJsonListTest extends TestCase
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

        $this->workUsers[] = factory(User::class)->create();
        factory(UserGroup::class)->create([
            'group_id' => $this->group->id,
            'user_id' => $this->workUsers[0]->id,
        ]);
        $this->workUsers[] = factory(User::class)->create();
        $this->workUsers[] = factory(User::class)->create();
        factory(UserGroup::class)->create([
            'group_id' => $this->group->id,
            'user_id' => $this->workUsers[2]->id,
        ]);
    }

    private function callTargetInterface(array $param = []) : TestResponse
    {
        return $this->ActingAs($this->user)->withSession([
            'contract_id' => $this->contract->id,
            'group_id' => $this->group->id,
        ])->json('GET', '/g-manage/user/json/', $param);
    }

    public function test取得できること()
    {
        $response = $this->callTargetInterface();
        $users = Group::find($this->group->id)->users()->orderBy('users.name')->get()->map(function($item, $key) {
            return [
                'id' => $item->id,
                'name' => $item->name,
            ];
        })->all();
        $response->assertOk()
        ->assertJson([
            'users' => $users
        ], true)->assertJsonCount(3, 'users');
    }
}
