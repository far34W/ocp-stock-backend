<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Article\ArticleResource;
use App\Http\Resources\Transfer\TransferResource;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService
    ) {}

    /**
     * GET /api/dashboard/stats
     */
    public function stats(): JsonResponse
    {
        return response()->json($this->dashboardService->stats());
    }

    /**
     * GET /api/dashboard/transfers-chart
     */
    public function transfersChart(): JsonResponse
    {
        return response()->json($this->dashboardService->transfersChart());
    }

    /**
     * GET /api/dashboard/categories
     */
    public function categories(): JsonResponse
    {
        return response()->json($this->dashboardService->categoriesBreakdown());
    }

    /**
     * GET /api/dashboard/recent-articles
     */
    public function recentArticles(): JsonResponse
    {
        $articles = $this->dashboardService->recentArticles();

        return response()->json(ArticleResource::collection($articles));
    }

    /**
     * GET /api/dashboard/recent-movements
     */
    public function recentMovements(): JsonResponse
    {
        $movements = $this->dashboardService->recentMovements();

        return response()->json(TransferResource::collection($movements));
    }

    /**
     * GET /api/dashboard/top-transferred
     */
    public function topTransferred(): JsonResponse
    {
        return response()->json($this->dashboardService->topTransferred());
    }

    /**
     * GET /api/dashboard/alerts
     */
    public function alerts(): JsonResponse
    {
        return response()->json($this->dashboardService->alerts());
    }
}
