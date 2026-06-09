<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Tag;
class ContactController extends Controller
{
    public function index()
    {
        $categories = Category::select('id', 'content')->get();

        $tags = Tag::select('id', 'name')->
            orderBy('id', 'asc')->get();

        return view('contact.index', compact('categories', 'tags'));
    }
    public function store(StoreContactRequest $request)
    {
        $validated = $request->validated();
        $tagId = is_array($request->tag_ids) ? head($request->tag_ids) : $request->tag_ids;
        $category = Category::find($tagId);
        return view('contact.confirm', compact('validated', 'category'));

    }
    //
}
