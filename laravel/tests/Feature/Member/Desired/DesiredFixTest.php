<?php

namespace Tests\Feature\Member\Desired;

use App\Eloquents\InputedDate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Member\MemberTestCase;
use Tests\TestCase;

class DesiredFixTest extends MemberTestCase
{
    public function test提出できること()
    {
        $week = 28;
        $date = '2020-07-01';
        $response = $this->ActingAsWithGroup(
            $this->user,
            $this->group,
            'POST',
            'member/desired/fix',
            [
                'date' => [$date],
                'week' => $week,
            ]);

        $response->assertRedirect(route('member.desired', compact('week')));

        $entity = InputedDate::whereGroupId($this->group->id)->whereUserId($this->user->id)->whereDateTarget($date)->first();
        $this->assertNotNull($entity);
    }
}
