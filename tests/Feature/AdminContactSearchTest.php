<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminContactSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $this->actingAs($user);
    }

    /** @test */
    public function 正常系：キーワード検索ができる()
    {
        $this->get('/admin?keyword=田中')->assertStatus(200);
    }

    /** @test */
    public function 正常系：性別検索ができる()
    {
        $this->get('/admin?gender=1')->assertStatus(200);
    }

    /** @test */
    public function 正常系：全条件なしで検索できる()
    {
        $this->get('/admin')->assertStatus(200);
    }

    /** @test */
    public function 異常系：不正な性別値を拒否する()
    {
        $this->get('/admin?gender=4')->assertStatus(302);
    }

    /** @test */
    public function 異常系：存在しないカテゴリ_i_dを拒否する()
    {
        $this->get('/admin?category_id=999')->assertStatus(302);
    }
}
