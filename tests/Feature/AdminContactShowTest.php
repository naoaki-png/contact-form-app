<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminContactShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 管理者は特定の問い合わせ詳細ページをカテゴリ情報付きで閲覧できる()
    {
        $user = User::factory()->create(['email' => 'test@example.com']);
        $category = Category::create(['content' => '商品トラブル']);
        $contact = Contact::factory()->create([
            'category_id' => $category->id,
            'first_name' => 'テスト',
            'last_name' => '太郎',
        ]);

        $response = $this->actingAs($user)
            ->get('/admin/contacts/'.$contact->id);

        $response->assertStatus(200)
            ->assertSee('テスト')
            ->assertSee('太郎')
            ->assertSee('商品トラブル');
    }
}
