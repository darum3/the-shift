<?php

namespace Tests\Feature\Mng;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;
use App\Eloquents\Contract;
use App\Eloquents\Group;
use App\Eloquents\UserGroup;

class GroupAddTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->contract = factory(Contract::class)->create();
        $this->group = factory(Group::class)->create([
            'contract_id' => $this->contract->id,
            'flg_admin' => true, // 管理グループ（契約ごとに1つ）
        ]);
        factory(UserGroup::class)->create([
            'group_id' => $this->group->id,
            'user_id' => $this->user->id,
        ]);
    }

    public function test入力画面()
    {
        $this->actingAs($this->user)->withSession(['contract_id' => $this->contract->id])
        ->get('/manage/group/add')
        ->assertOk()->assertViewIs('mng.group.add.input')
        ->assertViewHasAll([
            'fields' => [
                ['label' => '名前', 'width' => 4, 'name' => 'name'],
            ], 'action' => url("/manage/group/add/confirm")
        ]);
    }
}
