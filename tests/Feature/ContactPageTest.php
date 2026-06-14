<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactPageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function お問い合わせフォームが正常に表示されデータが渡されていること()
    {
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();

        $response = $this->get('/');
        $response->assertStatus(200);

        $response->assertViewHas('categories', function ($categories) use ($category) {
            return $categories->contains($category);
        });
        $response->assertViewHas('tags', function ($tags) use ($tag) {
            return $tags->contains($tag);
        });
    }

    /** @test */
    public function サンクスページが正常に表示されること()
    {
        $response = $this->get('/contacts/thanks');
        $response->assertStatus(200);
    }
}
