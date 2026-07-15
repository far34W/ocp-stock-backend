<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Transfer\TransferResource;
use App\Models\Transfer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TransferController extends Controller
{
    /**
     * GET /api/transfers
     * Query params: search (product name / person), date, per_page
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Transfer::with(['article.category', 'transferredBy'])
            ->latest();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('article', fn ($a) => $a->where('name', 'like', "%{$search}%"))
                  ->orWhere('person_name', 'like', "%{$search}%")
                  ->orWhere('to_location', 'like', "%{$search}%");
            });
        }

        if ($date = $request->get('date')) {
            $query->whereDate('created_at', $date);
        }

        if ($articleId = $request->get('article_id')) {
            $query->where('article_id', $articleId);
        }

        return TransferResource::collection(
            $query->paginate((int) $request->get('per_page', 15))
        );
    }

    /**
     * GET /api/transfers/{id}
     */
    public function show(Transfer $transfer): JsonResponse
    {
        $transfer->load(['article.category', 'transferredBy']);

        return response()->json(new TransferResource($transfer));
    }
}
