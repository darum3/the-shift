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
use Illuminate\Foundation\Testing\TestResponse;

class IndexTest extends TestCase
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

    private function callTargetInterface(array $param = []) : TestResponse
    {
        return $this->ActingAs($this->user)->withSession([
            'contract_id' => $this->contract->id,
            'group_id' => $this->group->id,
        ])->call('GET', '/g-manage/user/', $param);
    }

    public function test検索条件なし()
    {
        $this->callTargetInterface()->assertOk()
        ->assertViewIs('g-mng.user.list')
        ->assertViewHasAll([
            'group' => Group::findOrFail($this->group->id)->load('users'),
            'search' => null,
        ]);
    }

    public function testメールアドレス検索()
    {
        $this->callTargetInterface(['search' => $this->user->email])->assertOk()
        ->assertViewIs('g-mng.user.list')
        ->assertSeeText($this->user->email);
    }

    public function test名前検索()
    {
        $this->callTargetInterface(['search' => $this->user->name])->assertOk()
        ->assertViewIs('g-mng.user.list')
        ->assertSeeText($this->user->email);
    }
}
