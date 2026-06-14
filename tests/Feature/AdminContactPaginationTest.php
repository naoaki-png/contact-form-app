<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminContactPaginationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create());
    }

    /** @test */
    public function 正常系：検索条件を指定して絞り込みができること()
    {
        $category = Category::factory()->create();

        $targetContact = Contact::factory()->create([
            'first_name' => '田中',
            'gender' => 1,
            'category_id' => $category->id,
            'created_at' => '2026-06-14 10:00:00',
        ]);

        Contact::factory()->create([
            'first_name' => '佐藤',
            'gender' => 2,
        ]);

        $response = $this->get('/admin?keyword=田中&gender=1&category_id='.$category->id.'&date=2026-06-14');

        $response->assertStatus(200);
        $response->assertSee('田中');
        $response->assertDontSee('佐藤');
    }

    /** @test */
    public function 正常系：結果が7件ごとにページネーションされること()
    {
        Contact::factory()->count(8)->create();

        $response = $this->get('/admin?page=1');

        $response->assertSee('?page=2');

        $response2 = $this->get('/admin?page=2');
        $response2->assertStatus(200);
    }
}
