<?php

namespace Tests\Feature\GroupManage;

use App\User;
use App\Eloquents\Contract;
use App\Eloquents\Group;
use App\Eloquents\UserGroup;
use App\Eloquents\ShiftFixedDate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ShiftFixTest extends TestCase
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

        factory(ShiftFixedDate::class)->create([
            'group_id' => $this->group->id,
            'date_fixed' => today(),
        ]);
    }

    private function callInterface(Carbon $date, User $user = null) : TestResponse
    {
        $user = $user ?? $this->user;
        return $this->actingAs($user)->withSession([
            'contract_id' => $this->contract->id,
            'group_id' => $this->group->id,
        ])->json('POST', '/g-manage/shift/json/fix', [
            'date' => $date->toDateString(),
        ]);
    }

    public function testNormal()
    {
        $this->callInterface(today()->tomorrow())
        ->assertOk();

        $this->assertNotNull(ShiftFixedDate::whereGroupId($this->group->id)->whereDateFixed(today())->first());
    }

    public function test既に確定済み()
    {
        $this->callInterface(today())->assertStatus(409);
    }

    public function test権限なし()
    {
        $user = factory(User::class)->create();
        factory(UserGroup::class)->create([
            'group_id' => $this->group->id,
            'user_id' => $user->id,
        ]);

        $this->callInterface(today()->tomorrow(), $user)->assertStatus(403);
    }
}
