<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Article\StoreArticleRequest;
use App\Http\Requests\Article\UpdateArticleRequest;
use App\Http\Requests\Transfer\StoreTransferRequest;
use App\Http\Resources\Article\ArticleResource;
use App\Http\Resources\Transfer\TransferResource;
use App\Models\Article;
use App\Services\ArticleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ArticleController extends Controller
{
    public function __construct(
        private readonly ArticleService $articleService
    ) {}

    // ── CRUD ─────────────────────────────────────────────────────────────────

    /**
     * GET /api/articles
     * Query params: search, status, category_id, per_page
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $articles = $this->articleService->list(
            $request->only(['search', 'status', 'category_id']),
            (int) $request->get('per_page', 15)
        );

        return ArticleResource::collection($articles);
    }

    /**
     * POST /api/articles
     */
    public function store(StoreArticleRequest $request): JsonResponse
    {
        $article = $this->articleService->create($request->validated());

        return response()->json(new ArticleResource($article->load('category')), 201);
    }

    /**
     * GET /api/articles/{id}
     */
    public function show(Article $article): JsonResponse
    {
        return response()->json(new ArticleResource($article->load('category')));
    }

    /**
     * PUT /api/articles/{id}
     */
    public function update(UpdateArticleRequest $request, Article $article): JsonResponse
    {
        $article = $this->articleService->update($article, $request->validated());

        return response()->json(new ArticleResource($article));
    }

    /**
     * DELETE /api/articles/{id}  — soft delete
     */
    public function destroy(Article $article): JsonResponse
    {
        $this->articleService->softDelete($article);

        return response()->json(['message' => 'Article déplacé vers la corbeille.']);
    }

    // ── Trash management ─────────────────────────────────────────────────────

    /**
     * GET /api/articles/trashed
     */
    public function trashed(): AnonymousResourceCollection
    {
        $articles = Article::onlyTrashed()->with('category')->latest('deleted_at')->paginate(15);

        return ArticleResource::collection($articles);
    }

    /**
     * PUT /api/articles/{id}/restore
     */
    public function restore(int $id): JsonResponse
    {
        $article = $this->articleService->restore($id);

        return response()->json(new ArticleResource($article->load('category')));
    }

    /**
     * DELETE /api/articles/{id}/force
     */
    public function forceDestroy(int $id): JsonResponse
    {
        $this->articleService->forceDelete($id);

        return response()->json(['message' => 'Article définitivement supprimé.']);
    }

    // ── Scan ─────────────────────────────────────────────────────────────────

    /**
     * GET /api/articles/scan/{code}
     */
    public function scan(string $code): JsonResponse
    {
        $article = $this->articleService->findByCode($code);

        if (!$article) {
            return response()->json(['message' => 'Aucun article trouvé pour ce code.'], 404);
        }

        return response()->json(new ArticleResource($article));
    }

    // ── Transfer ─────────────────────────────────────────────────────────────

    /**
     * POST /api/articles/{id}/transfer
     */
    public function transfer(StoreTransferRequest $request, Article $article): JsonResponse
    {
        $transfer = $this->articleService->transfer(
            $article,
            $request->validated(),
            $request->user()->id
        );

        return response()->json(
            new TransferResource($transfer->load(['article.category', 'transferredBy'])),
            201
        );
    }
}
