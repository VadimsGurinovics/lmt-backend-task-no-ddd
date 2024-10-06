<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_set_id',
        'sku',
        'name',
        'slug',
        'type',
        'condition',
        'description_title',
        'description_text',
        'price',
        'wholesale_price',
        'published',
    ];

    /**
     * Event listener for creating and updating the model
     */
    public static function boot()
    {
        parent::boot();

        // Automatically generate slug when creating or updating a Product
        static::saving(function ($product) {
            // Only generate a slug if it's not set or if the name has changed
            if (empty($product->slug) || $product->isDirty('name')) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function productSet()
    {
        return $this->belongsTo(ProductSet::class);
    }
}
