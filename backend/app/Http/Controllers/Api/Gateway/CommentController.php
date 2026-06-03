<?php

namespace App\Http\Controllers\Api\Gateway;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Services\CommentService;
use Exception;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    public function __construct(private readonly CommentService $commentService) {}

    public function index(int|string $tutorialId): JsonResponse
    {
        try {
            $comments = $this->commentService->getByTutorial((int) $tutorialId);

            return ResponseHelper::success('Success', [
                'comments' => $comments->items(),
            ]);
        } catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), [], 400);
        }
    }

    public function store(StoreCommentRequest $request): JsonResponse
    {
        try {
            $comment = $this->commentService->create(
                auth()->id(),
                $request->validated(),
                auth()->user()->role
            );

            return ResponseHelper::created('Success', ['comment' => $comment]);
        } catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), [], $e->getCode() ?: 400);
        }
    }

    public function destroy(int|string $id): JsonResponse
    {
        try {
            $this->commentService->delete((int) $id, auth()->id(), auth()->user()->role);

            return ResponseHelper::success('Success');
        } catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), [], $e->getCode() ?: 400);
        }
    }
}
