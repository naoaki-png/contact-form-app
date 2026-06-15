<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = Category::factory()->create();
        $this->tags = Tag::factory()->count(3)->create();
    }

    /** @test*/
    public function ap_i検索バリデーション_不正な値を拒否する()
    {
        $response = $this->getJson('/api/v1/contacts?gender=4&per_page=999');
        $response->assertStatus(422);
    }

    /** @test*/
    public function お問い合わせ一覧_ap_i_正常に取得できる()
    {
        Contact::factory()->count(10)->create(['category_id' => $this->category->id]);

        $response = $this->getJson('/api/v1/contacts?page=1&per_page=7');
        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'meta' => ['current_page', 'last_page']]);
    }

    /** @test*/
    public function お問い合わせ作成_ap_i_正常に作成できる()
    {
        $data = [
            'first_name' => '田中',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'category_id' => $this->category->id,
            'detail' => 'テスト内容です',
            'tag_ids' => [$this->tags->first()->id],
        ];

        $response = $this->postJson('/api/v1/contacts', $data);
        $response->assertStatus(201);
        $this->assertDatabaseHas('contacts', ['email' => 'test@example.com']);
    }

    /** @test*/
    public function お問い合わせ詳細_ap_i_存在しない_i_dで404が返る()
    {
        $response = $this->getJson('/api/v1/contacts/999');
        $response->assertStatus(404)
            ->assertJson(['error' => 'お問い合わせが見つかりませんでした。']);
    }

    /** @test */
    public function お問い合わせ更新_ap_i_正常に更新できる()
    {
        $contact = Contact::factory()->create(['category_id' => $this->category->id]);
        $updateData = [
            'first_name' => '佐藤',
            'last_name' => '次郎',
            'gender' => 2,
            'email' => 'update@example.com',
            'tel' => '08012345678',
            'address' => '大阪府',
            'category_id' => $this->category->id,
            'detail' => '更新内容',
        ];

        $response = $this->putJson("/api/v1/contacts/{$contact->id}", $updateData);
        $response->assertStatus(200);
        $this->assertDatabaseHas('contacts', ['id' => $contact->id, 'first_name' => '佐藤']);
    }

    /** @test*/
    public function お問い合わせ削除_ap_i_正常に削除できる()
    {
        $contact = Contact::factory()->create();
        $response = $this->deleteJson("/api/v1/contacts/{$contact->id}");
        $response->assertStatus(204);
        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    }
}
