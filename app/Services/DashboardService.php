<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Category;
use App\Models\Transfer;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Global KPIs.
     */
    public function stats(): array
    {
        return [
            'total_articles'    => Article::count(),
            'total_categories'  => Category::count(),
            'total_stock'       => (int) Article::sum('quantity'),
            'low_stock_count'   => Article::where('status', 'low_stock')->count(),
            'out_of_stock_count'=> Article::where('status', 'out_of_stock')->count(),
        ];
    }

    /**
     * Transfers per day for the last 7 days.
     */
    public function transfersChart(): array
    {
        $days = collect(range(6, 0))->map(function (int $daysAgo) {
            $date = Carbon::today()->subDays($daysAgo);

            $count = Transfer::whereDate('created_at', $date)->count();
            $qty   = (int) Transfer::whereDate('created_at', $date)->sum('quantity');

            return [
                'date'      => $date->toDateString(),
                'transfers' => $count,
                'quantity'  => $qty,
            ];
        });

        return $days->values()->all();
    }

    /**
     * Stock breakdown per category.
     */
    public function categoriesBreakdown(): array
    {
        return Category::withCount('articles')
            ->withSum('articles', 'quantity')
            ->get()
            ->map(fn ($cat) => [
                'id'             => $cat->id,
                'name'           => $cat->name,
                'articles_count' => $cat->articles_count,
                'total_stock'    => (int) $cat->articles_sum_quantity,
            ])
            ->all();
    }

    /**
     * Latest 10 added articles.
     */
    public function recentArticles(): array
    {
        return Article::with('category')
            ->latest()
            ->limit(10)
            ->get()
            ->all();
    }

    /**
     * Latest 10 stock movements (transfers).
     */
    public function recentMovements(): array
    {
        return Transfer::with(['article', 'transferredBy'])
            ->latest()
            ->limit(10)
            ->get()
            ->all();
    }

    /**
     * Top 5 most transferred articles.
     */
    public function topTransferred(): array
    {
        return Article::withCount('transfers')
            ->withSum('transfers', 'quantity')
            ->orderByDesc('transfers_count')
            ->limit(5)
            ->get()
            ->map(fn ($a) => [
                'id'              => $a->id,
                'name'            => $a->name,
                'reference'       => $a->reference,
                'category'        => $a->category?->name,
                'transfers_count' => $a->transfers_count,
                'total_quantity'  => (int) $a->transfers_sum_quantity,
            ])
            ->all();
    }

    /**
     * Articles at or below min stock threshold.
     */
    public function alerts(): array
    {
        return Article::with('category')
            ->where('status', '!=', 'in_stock')
            ->orderBy('quantity')
            ->get()
            ->map(fn ($a) => [
                'id'           => $a->id,
                'name'         => $a->name,
                'quantity'     => $a->quantity,
                'min_quantity' => $a->min_quantity,
                'status'       => $a->status,
                'category'     => $a->category?->name,
            ])
            ->all();
    }
}
