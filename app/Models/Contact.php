<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Contact extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'gender',
        'email',
        'tel',
        'address',
        'building',
        'category_id',
        'detail',
    ];
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
    public function category()
    {
        // belongsTo は「〜に属する」という意味です
        return $this->belongsTo(Category::class);
    }
}
