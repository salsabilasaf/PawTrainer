<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Tutorial;

class CommentService
{
    /**
     * Tambah komentar pada tutorial.
     * User hanya bisa comment pada tutorial yang ada.
     */
    public function create(int $userId, array $data): Comment
    {
        // Pastikan tutorial ada
        Tutorial::findOrFail($data['tutorial_id']);

        $comment = Comment::create([
            'user_id'     => $userId,
            'tutorial_id' => $data['tutorial_id'],
            'comment'     => $data['comment'],
        ]);

        return $comment->load('user:id,name');
    }

    /**
     * Hapus komentar.
     * - Admin: bisa hapus komentar siapapun
     * - User: hanya bisa hapus komentar milik sendiri
     */
    public function delete(int $commentId, int $userId, string $role): void
    {
        $comment = Comment::findOrFail($commentId);

        if ($role !== 'admin' && $comment->user_id !== $userId) {
            throw new \Exception('Anda tidak memiliki izin untuk menghapus komentar ini.', 403);
        }

        $comment->delete();
    }

    /**
     * Ambil semua komentar pada sebuah tutorial.
     */
    public function getByTutorial(int $tutorialId): \Illuminate\Pagination\LengthAwarePaginator
    {
        Tutorial::findOrFail($tutorialId);

        return Comment::with('user:id,name')
            ->where('tutorial_id', $tutorialId)
            ->latest()
            ->paginate(15);
    }
}
