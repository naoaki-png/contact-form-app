<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Contact extends Model
{
    use HasFactory;

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
        return $this->belongsToMany(Tag::class, 'contact_tag');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeKeyword($query, $keyword)
    {
        if (! empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('first_name', 'like', "%{$keyword}%")
                    ->orWhere('last_name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");

            });
        }
    }

    public function scopeGender($query, $gender)
    {
        if (! empty($gender)) {
            $query->where('gender', $gender);
        }
    }

    public function scopeCategoryId($query, $categoryId)
    {
        if (! empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }
    }

    public function scopeDate($query, $date)
    {
        if (! empty($date)) {
            $query->whereDate('created_at', $date);
        }
    }
}
