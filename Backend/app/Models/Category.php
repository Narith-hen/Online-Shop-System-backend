<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image',
        'is_active'
    ];
    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = ['image_url'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }
        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }
        return url(Storage::url($this->image));
    }

    public function getProductsCountAttribute(): int
    {
        return $this->products()->count();
    }

}