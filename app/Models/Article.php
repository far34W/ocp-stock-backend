<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'reference',
        'barcode',
        'quantity',
        'min_quantity',
        'status',
        'article_status',
        'unit',
        'brand',
        'nature',
        'supplier',
        'ocp_code',
        'description',
        'category_id',
    ];

    protected $casts = [
        'quantity'     => 'integer',
        'min_quantity' => 'integer',
    ];

    // ── Boot: auto-compute stock status ─────────────────────────────────────

    protected static function booted(): void
    {
        static::saving(function (Article $article) {
            $article->status = self::computeStatus(
                $article->quantity,
                $article->min_quantity
            );
        });
    }

    public static function computeStatus(int $qty, int $min): string
    {
        if ($qty === 0) {
            return 'out_of_stock';
        }

        if ($qty <= $min) {
            return 'low_stock';
        }

        return 'in_stock';
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(Transfer::class);
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeSearch($query, ?string $term)
    {
        if (!$term) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('brand', 'like', "%{$term}%")
              ->orWhere('nature', 'like', "%{$term}%")
              ->orWhere('reference', 'like', "%{$term}%")
              ->orWhere('barcode', 'like', "%{$term}%");
        });
    }

    public function scopeByStatus($query, ?string $status)
    {
        return $status ? $query->where('status', $status) : $query;
    }

    public function scopeByCategory($query, ?int $categoryId)
    {
        return $categoryId ? $query->where('category_id', $categoryId) : $query;
    }

    public function scopeLowStock($query)
    {
        return $query->where('status', '!=', 'in_stock');
    }
}
