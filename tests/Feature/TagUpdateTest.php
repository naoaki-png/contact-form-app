<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function タグの更新ができる()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $tag1 = Tag::create(['name' => 'タグA']);
        $tag2 = Tag::create(['name' => 'タグB']);

        $this->put("/admin/tags/{$tag1->id}", ['name' => 'タグA'])
            ->assertStatus(302);

        $this->put("/admin/tags/{$tag1->id}", ['name' => 'タグB'])
            ->assertSessionHasErrors('name');
    }
}
