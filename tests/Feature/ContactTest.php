<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function お問い合わせはカテゴリに属しタグを同期できる()
    {
        $category = Category::factory()->create();
        $tags = Tag::factory()->count(3)->create();
        $contact = Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        $this->assertEquals($category->id, $contact->category->id);

        $contact->tags()->sync([$tags[0]->id, $tags[1]->id]);
        $this->assertCount(2, $contact->fresh()->tags);
        $this->assertTrue($contact->tags->contains($tags[0]->id));
        $this->assertTrue($contact->tags->contains($tags[1]->id));

        $contact->tags()->sync([$tags[1]->id, $tags[2]->id]);
        $freshTags = $contact->fresh()->tags;

        $this->assertCount(2, $freshTags);
        $this->assertTrue($freshTags->contains($tags[1]));
        $this->assertTrue($freshTags->contains($tags[2]));
        $this->assertFalse($freshTags->contains($tags[0]));
    }
}
