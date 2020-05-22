<?php

namespace Tests\Feature\GroupManage;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;
use App\Eloquents\Contract;
use App\Eloquents\Group;
use App\Eloquents\ShiftFixedDate;
use App\Eloquents\UserGroup;
use App\Eloquents\WorkType;
use Illuminate\Foundation\Testing\TestResponse;

class ShiftViewTest extends TestCase
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
        ])->call('GET', '/g-manage/shift/', $param);
    }

    public function test日付指定なし()
    {
        $response = $this->callTargetInterface();

        $response->assertOk()->assertViewIs('g-mng.shift.view')->assertViewHas('date', today()->tomorrow());
    }

    public function test日付指定なしデータあり()
    {
        factory(ShiftFixedDate::class)->create([
            'group_id' => $this->group->id,
            'date_fixed' => today()->tomorrow(),
        ]);

        $response = $this->callTargetInterface();
        $response->assertOk()->assertViewIs('g-mng.shift.view')->assertViewHas('date', today()->addDays(2));

    }

    public function test日付指定ありデータあり()
    {
        factory(ShiftFixedDate::class)->create([
            'group_id' => $this->group->id,
            'date_fixed' => today()->tomorrow(),
        ]);

        $response = $this->callTargetInterface(['date' => today()->tomorrow()->toDateString()]);
        $response->assertOk()->assertViewIs('g-mng.shift.view')->assertViewHas('date', today()->addDays(1));

    }
}
