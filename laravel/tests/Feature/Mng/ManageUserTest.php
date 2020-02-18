<?php

namespace Tests\Feature\Mng;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;
use App\Eloquents\Contract;
use App\Eloquents\Group;
use App\Eloquents\UserGroup;

class ManageUserTest extends TestCase
{
    private $user;
    private $contract;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->contract = factory(Contract::class)->create();
        $group = factory(Group::class)->create([
            'contract_id' => $this->contract->id,
        ]);
        factory(UserGroup::class)->create([
            'group_id' => $group->id,
            'user_id' => $this->user->id,
        ]);
    }

    public function testシステム管理にはアクセスできない確認()
    {
        $response = $this->actingAs($this->user)->get('/home');
        $response->assertOk();
        $response->assertDontSee('契約管理');

        $response = $this->actingAs($this->user)->get('/admin/contract')->assertForbidden();
    }

    public function testセッションに契約IDが入っているテスト()
    {
        $response = $this->post('/login', ['email' => $this->user->email, 'password' => 'password', '_token' => csrf_token()]);
        $response->assertRedirect('/home');
        $response->assertSessionHas('contract_id', $this->contract->id); // TODO エラーになる
    }
}
