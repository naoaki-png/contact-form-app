<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Contact;
class ContactController extends Controller
{
    public function index()
    {
        $categories = Category::select('id', 'content')->get();

        $tags = Tag::select('id', 'name')->
            orderBy('id', 'asc')->get();

        return view('contact.index', compact('categories', 'tags'));
    }
    public function confirm(StoreContactRequest $request)
    {
        $validated = $request->validated();

        $category = Category::findOrFail($validated['category_id']);

        $checkedTagIds = $validated['tag_ids'] ?? [];
        $tags = Tag::whereIn('id', $checkedTagIds)->get();

        return view('contact.confirm', compact('validated', 'category', 'tags'));
    }


    public function store(StoreContactRequest $request)
    {
        $validated = $request->validated();
        $contact = Contact::create($validated);
        if (isset($validated['tag_ids'])) {
            $contact->tags()->sync($validated['tag_ids']);
        }

        return redirect()->route('contact.thanks');
    }
}
