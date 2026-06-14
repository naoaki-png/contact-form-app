<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function カテゴリから紐づくお問い合わせが取得できる()
    {
        $category = Category::factory()->create();

        Contact::factory()->count(3)->create([
            'category_id' => $category->id,
        ]);

        $contacts = $category->contacts;

        $this->assertCount(3, $contacts);
        $this->assertInstanceOf(Contact::class, $contacts->first());
        $this->assertEquals($category->id, $contacts->first()->category_id);
    }
}
