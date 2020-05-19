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
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\Mime\Header\Headers;
use Tests\TestCase;

class ShiftInsertTest extends TestCase
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

    private function callInterface(array $param) : TestResponse
    {
        return $this->actingAs($this->user)->withSession([
            'contract_id' => $this->contract->id,
            'group_id' => $this->group->id,
        ])->json('POST', '/g-manage/shift/json/', $param);
    }

    public function test新規1件休憩なし()
    {
        $this->callInterface([
            [
                "date" => today()->toDateString(),
                "shift" => [
                    [
                        "user_id" => $this->workUsers[0]->id,
                        "work_type_code" => $this->workType->code,
                        "startTime" => "0900",
                        "endTime" => "1800",
                    ],
                ],
            ],
        ])->assertOk();

        $collect = Shift::whereGroupId($this->group->id)->whereUserId($this->workUsers[0]->id)->with('work_type')->get();
        $this->assertEquals(1, $collect->count());
        $entity = $collect->first();
        $this->assertEquals($this->workType->code, $entity->work_type->code);
        $this->assertEquals(today()->hour(9)->minute(0)->second(0)->toDateTimeString(), $entity->start_datetime);
        $this->assertEquals(today()->hour(18)->minute(0)->second(0)->toDateTimeString(), $entity->end_datetime);
    }

    public function test新規1件休憩1回()
    {
        $this->callInterface([
            [
                "date" => today()->toDateString(),
                "shift" => [
                    [
                        "user_id" => $this->workUsers[0]->id,
                        "work_type_code" => $this->workType->code,
                        "startTime" => "0900",
                        "endTime" => "1800",
                        "off_hours" => [
                            [
                                "startTime" => "1300",
                                "endTime" => "1400",
                            ],
                        ],
                    ],
                ],
            ],
        ])->assertOk();

        $collect = Shift::whereGroupId($this->group->id)->whereUserId($this->workUsers[0]->id)->with('work_type')->get();
        $this->assertEquals(1, $collect->count());
        $entity = $collect->first();
        $this->assertEquals($this->workType->code, $entity->work_type->code);
        $this->assertEquals(today()->hour(9)->minute(0)->second(0)->toDateTimeString(), $entity->start_datetime);
        $this->assertEquals(today()->hour(18)->minute(0)->second(0)->toDateTimeString(), $entity->end_datetime);

        $offHour = OffHour::whereShiftId($entity->id)->first();
        $this->assertEquals(today()->hour(13)->toDateTimeString(), $offHour->off_start_datetime);
        $this->assertEquals(today()->hour(14)->toDateTimeString(), $offHour->off_end_datetime);
    }

    public function test新規1件休憩2回()
    {
        $this->callInterface([
            [
                "date" => today()->toDateString(),
                "shift" => [
                    [
                        "user_id" => $this->workUsers[0]->id,
                        "work_type_code" => $this->workType->code,
                        "startTime" => "0900",
                        "endTime" => "2300",
                        "off_hours" => [
                            [
                                "startTime" => "1300",
                                "endTime" => "1400",
                            ],
                            [
                                "startTime" => "1600",
                                "endTime" => "1700",
                            ],
                        ],
                    ],
                ],
            ],
        ])->assertOk();

        $collect = Shift::whereGroupId($this->group->id)->whereUserId($this->workUsers[0]->id)->with('work_type')->get();
        $this->assertEquals(1, $collect->count());
        $entity = $collect->first();
        $this->assertEquals($this->workType->code, $entity->work_type->code);
        $this->assertEquals(today()->hour(9)->minute(0)->second(0)->toDateTimeString(), $entity->start_datetime);
        $this->assertEquals(today()->hour(23)->minute(0)->second(0)->toDateTimeString(), $entity->end_datetime);

        $offHourCollect = OffHour::whereShiftId($entity->id)->get();
        $this->assertEquals(2, $offHourCollect->count());
        $offHour = $offHourCollect->shift();
        $this->assertEquals(today()->hour(13)->toDateTimeString(), $offHour->off_start_datetime);
        $this->assertEquals(today()->hour(14)->toDateTimeString(), $offHour->off_end_datetime);
        $offHour = $offHourCollect->shift();
        $this->assertEquals(today()->hour(16)->toDateTimeString(), $offHour->off_start_datetime);
        $this->assertEquals(today()->hour(17)->toDateTimeString(), $offHour->off_end_datetime);
    }

    // public function testエラー新規1件休憩時間がシフトより前()
    // {
    //     $this->callInterface([
    //         [
    //             "date" => today()->toDateString(),
    //             "shift" => [
    //                 [
    //                     "user_id" => $this->workUsers[0]->id,
    //                     "work_type_code" => $this->workType->code,
    //                     "startTime" => "0900",
    //                     "endTime" => "2300",
    //                     "off_hours" => [
    //                         [
    //                             "startTime" => "0800",
    //                             "endTime" => "1000",
    //                         ],
    //                     ],
    //                 ],
    //             ],
    //         ],
    //     ])->assertStatus(400);
    // }

    // public function testエラー新規1件休憩時間がシフトより後()
    // {
    //     $this->callInterface([
    //         [
    //             "date" => today()->toDateString(),
    //             "shift" => [
    //                 [
    //                     "user_id" => $this->workUsers[0]->id,
    //                     "work_type_code" => $this->workType->code,
    //                     "startTime" => "0900",
    //                     "endTime" => "1800",
    //                     "off_hours" => [
    //                         [
    //                             "startTime" => "1730",
    //                             "endTime" => "1830",
    //                         ],
    //                     ],
    //                 ],
    //             ],
    //         ],
    //     ])->assertStatus(400);
    // }

    public function test新規2ユーザ()
    {
        $this->callInterface([
            [
                "date" => today()->toDateString(),
                "shift" => [
                    [
                        "user_id" => $this->workUsers[0]->id,
                        "work_type_code" => $this->workType->code,
                        "startTime" => "0900",
                        "endTime" => "2000",
                        "off_hours" => [
                            [
                                "startTime" => "1400",
                                "endTime" => "1500",
                            ],
                        ],
                    ],
                    [
                        "user_id" => $this->workUsers[1]->id,
                        "work_type_code" => $this->workType->code,
                        "startTime" => "1100",
                        "endTime" => "2300",
                        "off_hours" => [
                            [
                                "startTime" => "1500",
                                "endTime" => "1600",
                            ],
                        ],
                    ],
                ],
            ],
        ])->assertOk();

        $collect = Shift::whereGroupId($this->group->id)->whereUserId($this->workUsers[0]->id)->with('work_type')->get();
        $this->assertEquals(1, $collect->count());
        $entity = $collect->first();
        $this->assertEquals($this->workType->code, $entity->work_type->code);
        $this->assertEquals(today()->hour(9)->minute(0)->second(0)->toDateTimeString(), $entity->start_datetime);
        $this->assertEquals(today()->hour(20)->minute(0)->second(0)->toDateTimeString(), $entity->end_datetime);

        $offHour = OffHour::whereShiftId($entity->id)->first();
        $this->assertEquals(today()->hour(14)->toDateTimeString(), $offHour->off_start_datetime);
        $this->assertEquals(today()->hour(15)->toDateTimeString(), $offHour->off_end_datetime);

        $collect = Shift::whereGroupId($this->group->id)->whereUserId($this->workUsers[1]->id)->with('work_type')->get();
        $this->assertEquals(1, $collect->count());
        $entity = $collect->first();
        $this->assertEquals($this->workType->code, $entity->work_type->code);
        $this->assertEquals(today()->hour(11)->minute(0)->second(0)->toDateTimeString(), $entity->start_datetime);
        $this->assertEquals(today()->hour(23)->minute(0)->second(0)->toDateTimeString(), $entity->end_datetime);

        $offHour = OffHour::whereShiftId($entity->id)->first();
        $this->assertEquals(today()->hour(15)->toDateTimeString(), $offHour->off_start_datetime);
        $this->assertEquals(today()->hour(16)->toDateTimeString(), $offHour->off_end_datetime);
    }

    public function test日付2日間()
    {
        $this->callInterface([
            [
                "date" => today()->yesterday()->toDateString(),
                "shift" => [
                    [
                        "user_id" => $this->workUsers[0]->id,
                        "work_type_code" => $this->workType->code,
                        "startTime" => "0900",
                        "endTime" => "2000",
                        "off_hours" => [
                            [
                                "startTime" => "1400",
                                "endTime" => "1500",
                            ],
                        ],
                    ],
                ]
            ],[
                "date" => today()->toDateString(),
                "shift" => [
                    [
                        "user_id" => $this->workUsers[0]->id,
                        "work_type_code" => $this->workType->code,
                        "startTime" => "0900",
                        "endTime" => "2000",
                        "off_hours" => [
                            [
                                "startTime" => "1400",
                                "endTime" => "1500",
                            ],
                        ],
                    ],
                ]
            ]
        ])->assertOk();

        $collect = Shift::whereGroupId($this->group->id)->whereUserId($this->workUsers[0]->id)->with('work_type')->get();
        $this->assertEquals(2, $collect->count());
        $entity = $collect->shift();
        $this->assertEquals($this->workType->code, $entity->work_type->code);
        $this->assertEquals(today()->yesterday()->hour(9)->minute(0)->second(0)->toDateTimeString(), $entity->start_datetime);
        $this->assertEquals(today()->yesterday()->hour(20)->minute(0)->second(0)->toDateTimeString(), $entity->end_datetime);

        $offHour = OffHour::whereShiftId($entity->id)->first();
        $this->assertEquals(today()->yesterday()->hour(14)->toDateTimeString(), $offHour->off_start_datetime);
        $this->assertEquals(today()->yesterday()->hour(15)->toDateTimeString(), $offHour->off_end_datetime);

        $entity = $collect->shift();
        $this->assertEquals($this->workType->code, $entity->work_type->code);
        $this->assertEquals(today()->hour(9)->minute(0)->second(0)->toDateTimeString(), $entity->start_datetime);
        $this->assertEquals(today()->hour(20)->minute(0)->second(0)->toDateTimeString(), $entity->end_datetime);

        $offHour = OffHour::whereShiftId($entity->id)->first();
        $this->assertEquals(today()->hour(14)->toDateTimeString(), $offHour->off_start_datetime);
        $this->assertEquals(today()->hour(15)->toDateTimeString(), $offHour->off_end_datetime);
    }

    public function test更新1件()
    {
        $org = factory(Shift::class)->create([
            'group_id' => $this->group->id,
            'user_id' => $this->workUsers[0]->id,
            'work_type_id' => $this->workType->id,
            "start_datetime" => today()->hour(10)->toDateTimeString(),
            "end_datetime" => today()->hour(19)->toDateTimeString(),
        ]);
        $this->callInterface([
            [
                "date" => today()->toDateString(),
                "shift" => [
                    [
                        "user_id" => $this->workUsers[0]->id,
                        "work_type_code" => $this->workType->code,
                        "startTime" => "0900",
                        "endTime" => "2000",
                    ],
                ],
            ],
        ])->assertOk();

        $collect = Shift::whereGroupId($this->group->id)->whereUserId($this->workUsers[0]->id)->with('work_type')->get();
        $this->assertEquals(1, $collect->count());
        $entity = $collect->shift();
        $this->assertNotEquals($org->id, $entity->id);
        $this->assertEquals($this->workType->code, $entity->work_type->code);
        $this->assertEquals(today()->hour(9)->minute(0)->second(0)->toDateTimeString(), $entity->start_datetime);
        $this->assertEquals(today()->hour(20)->minute(0)->second(0)->toDateTimeString(), $entity->end_datetime);
    }

    public function test新規1件更新1件()
    {
        $org = factory(Shift::class)->create([
            'group_id' => $this->group->id,
            'user_id' => $this->workUsers[0]->id,
            'work_type_id' => $this->workType->id,
            "start_datetime" => today()->hour(10)->toDateTimeString(),
            "end_datetime" => today()->hour(19)->toDateTimeString(),
        ]);
        $this->callInterface([
            [
                "date" => today()->toDateString(),
                "shift" => [
                    [
                        "user_id" => $this->workUsers[0]->id,
                        "work_type_code" => $this->workType->code,
                        "startTime" => "0900",
                        "endTime" => "2000",
                    ],
                    [
                        "user_id" => $this->workUsers[1]->id,
                        "work_type_code" => $this->workType->code,
                        "startTime" => "1100",
                        "endTime" => "2200",
                    ],
                ],
            ],
        ])->assertOk();

        $collect = Shift::whereGroupId($this->group->id)->whereUserId($this->workUsers[0]->id)->with('work_type')->get();
        $this->assertEquals(1, $collect->count());
        $entity = $collect->shift();
        $this->assertNotEquals($org->id, $entity->id);
        $this->assertEquals($this->workType->code, $entity->work_type->code);
        $this->assertEquals(today()->hour(9)->minute(0)->second(0)->toDateTimeString(), $entity->start_datetime);
        $this->assertEquals(today()->hour(20)->minute(0)->second(0)->toDateTimeString(), $entity->end_datetime);

        $entity = Shift::whereGroupId($this->group->id)->whereUserId($this->workUsers[1]->id)->with('work_type')->first();
        $this->assertEquals($this->workType->code, $entity->work_type->code);
        $this->assertEquals(today()->hour(11)->minute(0)->second(0)->toDateTimeString(), $entity->start_datetime);
        $this->assertEquals(today()->hour(22)->minute(0)->second(0)->toDateTimeString(), $entity->end_datetime);
    }
}
