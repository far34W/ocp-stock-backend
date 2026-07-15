<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Transfer;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ArticleService
{
    /**
     * Paginated article list with optional filters.
     */
    public function list(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return Article::with('category')
            ->search($filters['search'] ?? null)
            ->byStatus($filters['status'] ?? null)
            ->byCategory($filters['category_id'] ?? null)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create article (status auto-computed by model boot).
     */
    public function create(array $data): Article
    {
        return Article::create($data);
    }

    /**
     * Update article fields.
     */
    public function update(Article $article, array $data): Article
    {
        $article->update($data);

        return $article->fresh('category');
    }

    /**
     * Soft-delete (move to trash).
     */
    public function softDelete(Article $article): void
    {
        $article->delete();
    }

    /**
     * Restore from trash.
     */
    public function restore(int $id): Article
    {
        $article = Article::withTrashed()->findOrFail($id);
        $article->restore();

        return $article;
    }

    /**
     * Permanently destroy.
     */
    public function forceDelete(int $id): void
    {
        $article = Article::withTrashed()->findOrFail($id);
        $article->forceDelete();
    }

    /**
     * Find by barcode or reference (scan feature).
     */
    public function findByCode(string $code): ?Article
    {
        return Article::with('category')
            ->where('barcode', $code)
            ->orWhere('reference', $code)
            ->first();
    }

    /**
     * Execute a stock transfer with full transactional safety.
     */
    public function transfer(Article $article, array $data, int $userId): Transfer
    {
        return DB::transaction(function () use ($article, $data, $userId) {

            $requestedQty = (int) $data['quantity'];

            if ($article->quantity < $requestedQty) {
                abort(422, "Quantité insuffisante. Stock disponible: {$article->quantity}");
            }

            $before = $article->quantity;
            $after  = $before - $requestedQty;

            // Update stock (model boot recomputes status automatically)
            $article->update(['quantity' => $after]);

            // Log transfer
            return Transfer::create([
                'article_id'      => $article->id,
                'transferred_by'  => $userId,
                'from_location'   => $data['from_location'] ?? null,
                'to_location'     => $data['to_location'],
                'person_name'     => $data['person_name'] ?? null,
                'quantity'        => $requestedQty,
                'quantity_before' => $before,
                'quantity_after'  => $after,
                'notes'           => $data['notes'] ?? null,
            ]);
        });
    }
}
