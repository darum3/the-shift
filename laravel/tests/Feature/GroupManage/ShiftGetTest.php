<?php

namespace Tests\Feature\GroupManage;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\User;
use App\Eloquents\Contract;
use App\Eloquents\Group;
use App\Eloquents\Shift;
use App\Eloquents\UserGroup;
use App\Eloquents\WorkType;
use Illuminate\Foundation\Testing\TestResponse;
use PHPUnit\Framework\TestResult;

class ShiftGetTest extends TestCase
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
        ])->json('GET', '/g-manage/shift/json/'.today()->toDateString(), $param);
    }

    public function test取得できること()
    {
        $shift = factory(Shift::class)->create([
            'group_id' => $this->group->id,
            'user_id' => $this->workUsers[0]->id,
            'work_type_id' => $this->workType->id,
            'start_datetime' => today()->hour(9)->minute(30),
            'end_datetime' => today()->hour(13)->minute(45),
        ]);
        $this->callTargetInterface()->assertOk()
        ->assertJson([
            [
                "date" => today()->toDateString(),
                "shifts" => [
                    [
                        "user_id" => $this->workUsers[0]->id,
                        "name" => $this->workUsers[0]->name,
                        "task" => [
                            "task_id" => $shift->id,
                            "startTime" => "0930",
                            "endTime" => "1345",
                            "work_type" => $this->workType->category,
                        ],
                    ],
                ],
            ],
        ], true);
    }
}
