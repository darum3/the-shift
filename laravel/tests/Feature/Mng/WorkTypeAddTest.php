<?php

namespace Tests\Feature\Mng;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;
use App\Eloquents\Contract;
use App\Eloquents\Group;
use App\Eloquents\UserGroup;

class WorkTypeAddTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->contract = factory(Contract::class)->create();
        $group = factory(Group::class)->create([
            'contract_id' => $this->contract->id,
            'flg_admin' => true,
        ]);
        factory(UserGroup::class)->create([
            'group_id' => $group->id,
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->withSession(['contract_id' => $this->contract->id]);
        $response = $this->actingAs($this->user)->get('/manage/work_type/add');

        $response->assertOk()->assertSeeTextInOrder(['職種追加', 'コード', '名称', '確認', '戻る']);
    }
}
