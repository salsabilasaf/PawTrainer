<?php

namespace App\Http\Controllers\Api\Gateway;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Favorite\StoreFavoriteRequest;
use App\Services\FavoriteService;
use Illuminate\Http\JsonResponse;

class FavoriteController extends Controller
{
    public function __construct(
        private readonly FavoriteService $favoriteService
    ) {}

    /**
     * GET /api/gateway/favorites
     * Akses: admin, user
     * Menampilkan semua tutorial favorit milik user yang sedang login.
     */
    public function index(): JsonResponse
    {
        $favorites = $this->favoriteService->getUserFavorites(auth()->id());

        return ResponseHelper::success('Daftar favorit berhasil diambil', [
            'favorites'  => $favorites->items(),
            'pagination' => [
                'current_page' => $favorites->currentPage(),
                'per_page'     => $favorites->perPage(),
                'total'        => $favorites->total(),
                'last_page'    => $favorites->lastPage(),
            ],
        ]);
    }

    /**
     * POST /api/gateway/favorites
     * Akses: admin, user
     * Toggle: tambah jika belum ada, hapus jika sudah ada.
     */
    public function store(StoreFavoriteRequest $request): JsonResponse
    {
        $result = $this->favoriteService->toggle(
            auth()->id(),
            $request->validated()['tutorial_id']
        );

        return ResponseHelper::success($result['message'], [
            'action'      => $result['action'],
            'tutorial_id' => $request->tutorial_id,
        ]);
    }
}
