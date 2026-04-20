<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'image',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category_id' => 'integer',
            'price' => 'integer',
            'stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the order items for the product.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the reviews for the product.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the user interactions for the product.
     */
    public function userInteractions(): HasMany
    {
        return $this->hasMany(UserInteraction::class);
    }

    /**
     * Get the images for the product.
     */
    public function productImages(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Scope a query to only active products.
     */
    public function scopeActiveProducts($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Format the product price as Rupiah.
     */
    protected function formatPrice(): Attribute
    {
        return Attribute::make(
            get: fn() => 'Rp ' . number_format($this->price, 0, ',', '.')
        );
    }

    /**
     * Get the average rating from product reviews.
     */
    public function averageRating(): float
    {
        if ($this->relationLoaded('reviews')) {
            return (float) $this->reviews->avg('rating') ?: 0.0;
        }

        return (float) $this->reviews()->avg('rating') ?: 0.0;
    }
}
