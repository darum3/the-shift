<?php

namespace Tests\Feature\GroupManage;

use App\Eloquents\Group;
use App\Eloquents\Shift;
use App\Eloquents\UserGroup;
use App\Eloquents\Contract;
use App\Eloquents\OffHour;
use App\Eloquents\WorkType;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\TestResponse;

class ShiftDeleteTest extends TestCase
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

        $this->shift = factory(Shift::class)->create([
            'group_id' => $this->group->id,
            'user_id' => $this->workUsers[0]->id,
            'work_type_id' => $this->workType->id,
            'start_datetime' => today()->tomorrow()->hour(9)->minute(0),
            'end_datetime' => today()->tomorrow()->hour(18)->minute(0)
        ]);
    }

    private function callInterface(array $param) : TestResponse
    {
        return $this->actingAs($this->user)->withSession([
            'contract_id' => $this->contract->id,
            'group_id' => $this->group->id,
        ])->json('DELETE', '/g-manage/shift/json/', $param);
    }

    public function testValidationTaskIdNotExists()
    {
        $this->callInterface([
            'task_id' => [1],
        ])->assertStatus(422);

        $this->callInterface([
            'task_id' => [0, $this->shift->id],
        ])->assertStatus(422);
    }

    public function test削除OK()
    {
        $this->callInterface([
            'task_id' => [$this->shift->id]
        ])->assertOk();

        $entity = Shift::withTrashed()->find($this->shift->id);
        $this->assertNotNull($entity->deleted_at);
    }
}
