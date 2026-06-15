<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCsvExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function 異常系：不正な性別やカテゴリ_i_dを拒否する()
    {
        $this->actingAs($this->user);

        $this->get('/contacts/export?gender=4')->assertStatus(302);

        $this->get('/contacts/export?category_id=999')->assertStatus(302);
    }

    /** @test */
    public function 正常系：フィルタ条件付きで_cs_vダウンロードできる()
    {
        $this->actingAs($this->user);
        $category = Category::factory()->create();

        $response = $this->get("/contacts/export?gender=1&category_id={$category->id}");

        $response->assertStatus(200);

        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    /** @test */
    public function 正常系：条件指定なしで_cs_vダウンロードできる()
    {
        $this->actingAs($this->user);

        $response = $this->get('/contacts/export');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }
}
