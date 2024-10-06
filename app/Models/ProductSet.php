<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductSet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'published',
    ];

    /**
     * Event listener for creating and updating the model
     */
    public static function boot()
    {
        parent::boot();

        // Automatically generate slug when creating or updating a ProductSet
        static::saving(function ($productSet) {
            // Only generate a slug if it's not set or if the name has changed
            if (empty($productSet->slug) || $productSet->isDirty('name')) {
                $productSet->slug = Str::slug($productSet->name);
            }
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function isPublished(): bool
    {
        return $this->products()
            ->where('published', true)
            ->exists();
    }
}

