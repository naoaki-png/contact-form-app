<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Models\Tag;

class TagController extends Controller
{
    public function edit($id)
    {
        $tag = Tag::findOrFail($id);

        return view('admin.tags.edit', compact('tag'));

    }

    public function store(StoreTagRequest $request)
    {
        Tag::create($request->validated());

        return redirect()->route('admin.index')->withFragment('tag-section')->with('success', 'タグを追加しました');

    }

    public function destroy($id)
    {
        $tag = Tag::findOrFail($id);
        $tag->delete();

        return redirect()->route('admin.index')->withFragment('tag-section')->with('message', 'タグを削除しました。');
    }

    //
    public function update(UpdateTagRequest $request, $id)
    {
        $tag = Tag::findOrFail($id);
        $tag->update($request->validated());

        return redirect()->route('admin.index')->withFragment('tag-section')->with('message', 'タグを削除しました。');
    }
}
