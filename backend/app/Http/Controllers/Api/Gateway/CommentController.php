<?php

namespace App\Http\Controllers\Api\Gateway;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct(
        private readonly CommentService $commentService
    ) {}

    /**
     * GET /api/gateway/tutorials/{tutorialId}/comments
     * Akses: admin, user
     */
    public function index(int $tutorialId): JsonResponse
    {
        $comments = $this->commentService->getByTutorial($tutorialId);

        return ResponseHelper::success('Daftar komentar berhasil diambil', [
            'comments'   => $comments->items(),
            'pagination' => [
                'current_page' => $comments->currentPage(),
                'per_page'     => $comments->perPage(),
                'total'        => $comments->total(),
                'last_page'    => $comments->lastPage(),
            ],
        ]);
    }

    /**
     * POST /api/gateway/comments
     * Akses: admin, user
     */
    public function store(StoreCommentRequest $request): JsonResponse
    {
        $comment = $this->commentService->create(
            auth()->id(),
            $request->validated()
        );

        return ResponseHelper::created('Komentar berhasil ditambahkan', [
            'comment' => $comment,
        ]);
    }

    /**
     * DELETE /api/gateway/comments/{id}
     * Akses: admin (hapus siapapun), user (hanya milik sendiri)
     */
    public function destroy(int $id): JsonResponse
    {
        $this->commentService->delete(
            $id,
            auth()->id(),
            auth()->user()->role
        );

        return ResponseHelper::success('Komentar berhasil dihapus');
    }
}
