<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 一つのタグが複数のお問い合わせに紐づくこと()
    {
        $tag = Tag::factory()->create(['name' => '質問']);

        $contact1 = Contact::factory()->create();
        $contact2 = Contact::factory()->create();

        $tag->contacts()->attach([$contact1->id, $contact2->id]);

        $this->assertEquals(2, $tag->contacts->count());
        $this->assertTrue($tag->contacts->contains($contact1));
        $this->assertTrue($tag->contacts->contains($contact2));
    }
}
