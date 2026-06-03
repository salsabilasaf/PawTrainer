<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Tutorial;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;

class CommentService
{
    public function create(int $userId, array $data, string $role = 'user'): Comment
    {
        Tutorial::findOrFail($data['tutorial_id']);

        if (!empty($data['parent_id']) && $role !== 'admin') {
            throw new Exception('Hanya admin yang dapat membalas komentar.', 403);
        }

        $comment = Comment::create([
            'user_id' => $userId,
            'tutorial_id' => $data['tutorial_id'],
            'parent_id' => $data['parent_id'] ?? null,
            'comment' => $data['comment'],
        ]);

        return $comment->load('user:id,name');
    }

    public function delete(int $commentId, int $userId, string $role): void
    {
        $comment = Comment::findOrFail($commentId);

        if ($role !== 'admin' && $comment->user_id !== $userId) {
            throw new Exception('Anda tidak memiliki izin untuk menghapus komentar ini.', 403);
        }

        $comment->delete();
    }

    public function getByTutorial(int $tutorialId): LengthAwarePaginator
    {
        Tutorial::findOrFail($tutorialId);

        return Comment::with(['user:id,name', 'replies.user:id,name'])
            ->where('tutorial_id', $tutorialId)
            ->whereNull('parent_id')
            ->latest()
            ->paginate(15);
    }
}
