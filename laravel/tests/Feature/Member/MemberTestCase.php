<?php

namespace Tests\Feature\Member;

use App\Eloquents\Group;
use App\User;
use App\Eloquents\Contract;
use App\Eloquents\UserGroup;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;

abstract class MemberTestCase extends TestCase
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
        ]);
    }

    protected function ActingAsWithGroup(User $user, Group $group, string $method, string $path, array $param = []): TestResponse
    {
        return $this->actingAs($user)->withSession([
            'contract_id' => $group->contract_id,
            'group_id' => $group->id,
            'group_name' => $group->name,
        ])->call($method, $path, $param);
    }
}
