<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    /**
     * GET /api/categories
     */
    public function index(): AnonymousResourceCollection
    {
        $categories = Category::withCount('articles')
            ->withSum('articles', 'quantity')
            ->latest()
            ->get();

        return CategoryResource::collection($categories);
    }

    /**
     * POST /api/categories
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = Category::create($request->validated());

        return response()->json(new CategoryResource($category), 201);
    }

    /**
     * GET /api/categories/{id}
     */
    public function show(Category $category): JsonResponse
    {
        $category->loadCount('articles');

        return response()->json(new CategoryResource($category));
    }

    /**
     * PUT /api/categories/{id}
     */
    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $category->update($request->validated());

        return response()->json(new CategoryResource($category->fresh()));
    }

    /**
     * DELETE /api/categories/{id}
     */
    public function destroy(Category $category): JsonResponse
    {
        if ($category->articles()->count() > 0) {
            return response()->json([
                'message' => 'Impossible de supprimer une catégorie contenant des articles.',
            ], 422);
        }

        $category->delete();

        return response()->json(['message' => 'Catégorie supprimée.']);
    }
}
