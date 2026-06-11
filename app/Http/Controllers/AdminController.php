<?php

namespace App\Http\Controllers;
use App\Models\Tag;
use App\Models\Category;
use App\Models\Contact;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $tags = Tag::all();
        $contacts = Contact::with('category')->paginate(7);

        return view('admin.index', compact('categories', 'tags', 'contacts'));
    }
    //
}
