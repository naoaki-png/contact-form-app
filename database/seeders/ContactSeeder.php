<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    public function run()
    {
        $categories = Category::all();
        $tags = Tag::all();

        if ($categories->isEmpty() || $tags->isEmpty()) {
            return;
        }
        for ($i = 0; $i < 20; $i++) {
            $contact = Contact::factory()->create([
                'category_id' => $categories->random()->id,
            ]);

            $takeCount = min($tags->count(), rand(1, 3));
            $randomTags = $tags->random($takeCount);
            $contact->tags()->attach($randomTags->pluck('id'));
        }
    }
}
