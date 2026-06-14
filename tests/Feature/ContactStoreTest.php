<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactStoreTest extends TestCase
{
    use RefreshDatabase;

    /**
    @test
     */
    public function 正常系：全ての必須項目とタグを入力して保存できる(): void
    {
        $category = Category::factory()->create(['id' => 1]);

        Tag::factory()->count(3)->create();
        $data = [
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京',
            'building' => 'ビル1',
            'category_id' => 1,
            'detail' => 'テストのお問い合わせ内容です。',
            'tag_ids' => [1, 2, 3],
        ];

        $response = $this->post('/contacts', $data);

        $response->assertStatus(302);
        $this->assertDatabaseHas('contacts', [
            'first_name' => '山田',
            'last_name' => '太郎',
        ]);

    }

    /** @test */
    public function 異常系：不正な電話番号形式は拒否される()
    {
        $category = Category::factory()->create([
            'content' => '商品のお届けについて',
        ]);

        $data = [
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => 'abc-defg-hijk',
            'address' => '東京',
            'building' => 'ビル1',
            'category_id' => $category->id,
            'detail' => 'テストのお問い合わせ内容です。',
            'tag_ids' => [1, 2],
        ];

        $response = $this->post('/contacts', $data);

        $response->assertSessionHasErrors(['tel']);
    }
}
