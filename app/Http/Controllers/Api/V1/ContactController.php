<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\IndexContactRequest;
use App\Http\Requests\Api\V1\StoreContactRequest;
use App\Http\Requests\Api\V1\UpdateContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ContactController extends Controller
{
    public function index(IndexContactRequest $request): AnonymousResourceCollection
    {
        $query = Contact::with(['category', 'tags']);

        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->where(function ($q) use ($keyword) {
                $q->where('first_name', 'like', "%{$keyword}%")
                    ->orWhere('last_name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->input('gender'));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->input('date'));
        }

        $perPage = $request->input('per_page', 20);

        $contacts = $query->latest()->paginate($perPage);

        return ContactResource::collection($contacts);
    }

    public function store(StoreContactRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $contact = Contact::create(collect($validated)->except('tag_ids')->toArray());

        if ($request->has('tag_ids')) {
            $contact->tags()->attach($request->input('tag_ids'));
        }

        $contact->load(['category', 'tags']);

        return (new ContactResource($contact))
            ->response()
            ->setStatusCode(201);
    }

    public function show($id): JsonResponse|ContactResource
    {
        $contact = Contact::with(['category', 'tags'])->find($id);

        if (! $contact) {
            return response()->json(['error' => 'お問い合わせが見つかりませんでした。'], 404);
        }

        return new ContactResource($contact);
    }

    public function update(UpdateContactRequest $request, $id): JsonResponse|ContactResource
    {
        $contact = Contact::find($id);

        if (! $contact) {
            return response()->json(['error' => 'お問い合わせが見つかりませんでした。'], 404);
        }

        $validated = $request->validated();

        $contact->update(collect($validated)->except('tag_ids')->toArray());

        if ($request->has('tag_ids')) {
            $contact->tags()->sync($request->input('tag_ids'));
        } else {
            $contact->tags()->sync([]);
        }

        $contact->load(['category', 'tags']);

        return new ContactResource($contact);
    }

    public function destroy($id): JsonResponse
    {
        $contact = Contact::find($id);

        if (! $contact) {
            return response()->json(['error' => 'お問い合わせが見つかりませんでした。'], 404);
        }

        $contact->delete();

        return response()->json(null, 204);
    }
}
