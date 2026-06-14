<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactConfirmTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function バリデーション通過時に確認ページが表示されデータが表示されること()
    {
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();

        $data = [
            'first_name' => '田中',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区',
            'building' => 'ビル名',
            'category_id' => $category->id,
            'detail' => 'テストのお問い合わせ内容です。',
            'tag_ids' => [$tag->id],
        ];

        $response = $this->post('/contacts/confirm', $data);

        $response->assertStatus(200);

        $response->assertSee('田中');
        $response->assertSee('test@example.com');
        $response->assertSee($category->content);
    }

    /** @test */
    public function バリデーションエラー時は元のページにリダイレクトされること()
    {
        $response = $this->post('/contacts/confirm', []);

        $response->assertStatus(302);

        $response->assertSessionHasErrors(['first_name', 'last_name', 'email']);
    }
}
