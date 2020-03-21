<?php

namespace Tests\Feature\Mng;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;
use App\Eloquents\Contract;
use App\Eloquents\Group;
use App\Eloquents\UserGroup;

class GroupListTest extends TestCase
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
            // 'flg_admin' => true,
        ]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test一覧表示()
    {
        $this->actingAs($this->user);
        $this->withSession(['contract_id' => $this->contract->id]);
        $response = $this->get('/manage/group');

        $response->assertOk()->assertSeeText('グループ一覧')->assertSeeText('追加');
    }
}
