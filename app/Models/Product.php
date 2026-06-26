<?php

namespace App\Models;

use App\Helpers\SocketHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = [
        'name',
        'image',
        'price',
        'stock',
        'category_id',
        'is_active'
    ];
    /**
     * Requirement A: Enforce Attribute Casting ($casts)
     * Direct database values are parsed into actual JSON floats, integers, and booleans.
     */
    protected $casts = [
        'price'     => 'float',
        'stock'     => 'integer',
        'is_active' => 'boolean',
    ];
    /**
     * Requirement A: Append custom virtual field to the JSON string serialization
     */
    protected $appends = ['image_url'];
    /**
     * Requirement A: One-to-Many Inverse Link (belongsTo Relationship)
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function wishlistItems(): HasMany
    {
        return $this->hasMany(WishlistItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Requirement A: Accessor for generating absolute external URLs for client 
applications
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }
        return url(Storage::url($this->image));
    }

    protected static function booted(): void
    {
        static::created(function (Product $product) {
            try {
                $notif = Notification::create([
                    'title'   => 'New Product: ' . $product->name,
                    'message' => '$' . number_format($product->price, 2) . ' — ' . ($product->category?->name ?? 'Uncategorized'),
                    'type'    => 'new_product',
                    'link'    => '/products/' . $product->id,
                ]);

                $userIds = User::where('notifications_enabled', true)
                    ->whereHas('role', fn ($q) => $q->where('name', 'customer'))
                    ->pluck('id');

                $now = now();
                $pivot = $userIds->map(fn ($uid) => [
                    'notification_id' => $notif->id,
                    'user_id'         => $uid,
                    'read_at'         => null,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ]);

                DB::table('notification_reads')->insertOrIgnore($pivot->toArray());

                SocketHelper::notification([
                    'id'         => $notif->id,
                    'title'      => $notif->title,
                    'message'    => $notif->message,
                    'type'       => $notif->type,
                    'link'       => $notif->link,
                    'created_at' => $notif->created_at->toIso8601String(),
                ]);
            } catch (\Throwable $e) {
                Log::warning('Failed to create product notification: ' . $e->getMessage());
            }
        });
    }
}