<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTagTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 未認証ユーザーはタグ操作が拒否されログイン画面へリダイレクトされる()
    {
        $tag = Tag::factory()->create();

        $this->get('/admin/tags/'.$tag->id.'/edit')->assertRedirect('/login');
        $this->post('/admin/tags', ['name' => 'テスト'])->assertRedirect('/login');
        $this->put('/admin/tags/'.$tag->id, ['name' => '更新'])->assertRedirect('/login');
        $this->delete('/admin/tags/'.$tag->id)->assertRedirect('/login');
    }

    /** @test */
    public function 認証済みユーザーはタグ操作を行い管理画面へリダイレクトされる()
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create(['name' => '初期タグ']);

        $this->actingAs($user)->get('/admin/tags/'.$tag->id.'/edit')->assertStatus(200);

        $this->actingAs($user)->post('/admin/tags', ['name' => '新規タグ'])
            ->assertRedirect('/admin#tag-section');
        $this->assertDatabaseHas('tags', ['name' => '新規タグ']);

        $this->actingAs($user)->put('/admin/tags/'.$tag->id, ['name' => '更新タグ'])
            ->assertRedirect('/admin#tag-section');
        $this->assertDatabaseHas('tags', ['name' => '更新タグ']);

        $this->actingAs($user)->delete('/admin/tags/'.$tag->id)
            ->assertRedirect('/admin#tag-section');
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }
}
