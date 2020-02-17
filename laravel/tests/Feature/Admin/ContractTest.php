<?php

namespace Tests\Feature\Admin;

use App\Eloquents\Contract;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContractTest extends TestCase
{
    private $user;
    private $contract;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create(['flg_system_admin' => true]);
        $this->contract = factory(Contract::class)->create();
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test一覧が表示できること()
    {
        $response = $this->actingAs($this->user)->get('/admin/contract');
        $response->assertStatus(200);

        $response->assertSee($this->contract->name);
    }

    public function test契約追加画面表示()
    {
        $response = $this->actingAs($this->user)->get('/admin/contract/input');
        $response->assertStatus(200);
        $response->assertViewIs('admin.contract.input');
    }

    public function test詳細が表示できること() {
        $response = $this->actingAs($this->user)->get('/admin/contract/');
        $response->assertOk();

        $response->assertSeeTextInOrder([$this->contract->name, '詳細']);
        $response->assertDontSee('TODO');

        $response = $this->actingAs($this->user)->get('/admin/contract/show/'.$this->contract->id);
        $response->assertOk()->assertViewIs('admin.contract.show')->assertSee(htmlspecialchars('【'.$this->contract->name.'】', ENT_QUOTES));

    }
}
