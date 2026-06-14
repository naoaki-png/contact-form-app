<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactCreateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 正常系：全ての必須項目とタグを入力して保存できること()
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

        $response = $this->post('/contacts', $data);

        $response->assertRedirect('/contacts/thanks');

        $this->assertDatabaseHas('contacts', [
            'first_name' => '田中',
            'email' => 'test@example.com',
            'category_id' => $category->id,
        ]);

        $contact = Contact::where('email', 'test@example.com')->first();
        $this->assertDatabaseHas('contact_tag', [
            'contact_id' => $contact->id,
            'tag_id' => $tag->id,
        ]);
    }

    /** @test */
    public function 異常系：バリデーションエラー時は元のページにリダイレクトされること()
    {
        $response = $this->post('/contacts', []);

        $response->assertStatus(302);

        $response->assertSessionHasErrors(['first_name', 'last_name', 'email', 'category_id', 'detail']);
    }
}
