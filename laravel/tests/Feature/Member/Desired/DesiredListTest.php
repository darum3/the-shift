<?php

namespace Tests\Feature\Member\Desired;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Member\MemberTestCase;
use Tests\TestCase;

class DesiredListTest extends MemberTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }
    public function test標準画面表示()
    {
        $start = today()->startOfWeek()->toImmutable();
        $response = $this->ActingAsWithGroup($this->user, $this->group, 'GET', '/member/desired');
        $response->assertOk()
        ->assertSeeText('シフト提出状況：'.$start->format('m/d').'〜'.$start->addDay(6)->format('m/d'));
    }
}
