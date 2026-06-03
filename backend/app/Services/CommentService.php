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

        $payload = [
            'user_id' => $userId,
            'tutorial_id' => $data['tutorial_id'],
            'comment' => $data['comment'],
        ];

        if (!empty($data['parent_id'])) {
            $payload['parent_id'] = $data['parent_id'];
        }

        $comment = Comment::create($payload);

        return $comment->load('user:id,name');
    }

    public function delete(int $commentId, string $role): void
    {
        $comment = Comment::findOrFail($commentId);

        if ($role !== 'admin') {
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
