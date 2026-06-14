<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagStoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function タグの新規登録ができる()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/admin/tags', ['name' => '新しいタグ']);
        $response->assertStatus(302);
        $this->assertDatabaseHas('tags', ['name' => '新しいタグ']);
    }

    /** @test */
    public function タグ名の必須入力・文字数・一意性バリデーションテスト()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post('/admin/tags', ['name' => ''])->assertSessionHasErrors('name');

        $this->post('/admin/tags', ['name' => str_repeat('a', 51)])->assertSessionHasErrors('name');

        Tag::create(['name' => '重複タグ']);
        $this->post('/admin/tags', ['name' => '重複タグ'])->assertSessionHasErrors('name');
    }
}
