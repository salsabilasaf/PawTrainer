<?php

namespace App\Http\Controllers\Api\Gateway;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService
    ) {}

    /**
     * GET /api/gateway/categories
     * Akses: admin, user
     */
    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getAll();

        return ResponseHelper::success('Daftar kategori berhasil diambil', [
            'categories' => $categories,
        ]);
    }

    /**
     * GET /api/gateway/categories/{id}
     * Akses: admin, user
     */
    public function show(int $id): JsonResponse
    {
        $category = $this->categoryService->findById($id);

        return ResponseHelper::success('Detail kategori berhasil diambil', [
            'category' => $category,
        ]);
    }

    /**
     * POST /api/gateway/categories
     * Akses: admin only
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->create($request->validated());

        return ResponseHelper::created('Kategori berhasil dibuat', [
            'category' => $category,
        ]);
    }

    /**
     * PUT /api/gateway/categories/{id}
     * Akses: admin only
     */
    public function update(UpdateCategoryRequest $request, int $id): JsonResponse
    {
        $category = $this->categoryService->update($id, $request->validated());

        return ResponseHelper::success('Kategori berhasil diperbarui', [
            'category' => $category,
        ]);
    }

    /**
     * DELETE /api/gateway/categories/{id}
     * Akses: admin only
     */
    public function destroy(int $id): JsonResponse
    {
        if (auth()->user()->role !== 'admin') {
            return ResponseHelper::forbidden('Hanya admin yang dapat menghapus kategori.');
        }

        $this->categoryService->delete($id);

        return ResponseHelper::success('Kategori berhasil dihapus');
    }
}
