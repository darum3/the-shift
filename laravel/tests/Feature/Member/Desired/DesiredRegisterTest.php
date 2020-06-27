<?php

namespace Tests\Feature\Member\Desired;

use App\Eloquents\DesiredShift;
use App\Eloquents\WorkType;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Member\MemberTestCase;
use Tests\TestCase;

class DesiredRegisterTest extends MemberTestCase
{
    public function test登録できること()
    {
        $response = $this->JsonActingAsWithGroup($this->user, $this->group, 'POST',
            'member/desired/json/register',
            [
                [
                    'target_date' => today()->toDateString(),
                    'desired' => [
                        [
                            'work_type' => $this->workType->code,
                            'start' => '0900',
                            'end' => '1800',
                        ],
                    ],
                ],
            ]
        );
        $response->assertOk();

        $entity = DesiredShift::where([
            'group_id' => $this->group->id,
            'user_id' => $this->user->id,
            'date_target' => today()
        ])->get();
        $this->assertCount(1, $entity);
        $data = $entity->first();
        $this->assertEquals($this->workType->id, $data->work_type_id);
        $this->assertEquals(CarbonImmutable::createFromTime(9, 0)->toTimeString(), $data->time_start);
        $this->assertEquals(CarbonImmutable::createFromTime(18, 0)->toTimeString(), $data->time_end);
    }
}
