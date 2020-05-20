<?php

namespace Tests\Feature\Mng\Group;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Eloquents\Group;
use App\User;
use App\Eloquents\Contract;
use App\Eloquents\UserGroup;

class AdminAddInputTest extends TestCase
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

    public function test入力画面表示()
    {
        $this->actingAs($this->user)->withSession(['contract_id' => $this->contract->id]);
        $response = $this->get("/manage/group/{$this->group->id}/admin_add");
        $response->assertOk()->assertViewIs('mng.group.admin_add.input')->assertViewHasAll([
            'group' => Group::find($this->group->id),
            'fields' => [
                ['label' => '名前', 'width' => 4, 'name' => 'user_name'],
                ['label' => 'メールアドレス', 'width' => 4, 'name' => 'email'],
                ['label' => 'パスワード', 'type' => 'password', 'width' => 4, 'name' => 'password'],
            ], 'action' => url("/manage/group/{$this->group->id}/admin_add/confirm"),
        ]);
    }
}
