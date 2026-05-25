<?php

namespace App\Services;

use App\Models\Favorite;
use App\Models\Tutorial;
use Illuminate\Pagination\LengthAwarePaginator;

class FavoriteService
{
    /**
     * Toggle favorite: tambah jika belum ada, hapus jika sudah ada.
     */
    public function toggle(int $userId, int $tutorialId): array
    {
        // Pastikan tutorial ada
        Tutorial::findOrFail($tutorialId);

        $existing = Favorite::where('user_id', $userId)
            ->where('tutorial_id', $tutorialId)
            ->first();

        if ($existing) {
            $existing->delete();
            return [
                'action'  => 'removed',
                'message' => 'Tutorial dihapus dari favorit',
            ];
        }

        Favorite::create([
            'user_id'     => $userId,
            'tutorial_id' => $tutorialId,
        ]);

        return [
            'action'  => 'added',
            'message' => 'Tutorial ditambahkan ke favorit',
        ];
    }

    /**
     * Ambil semua tutorial favorit milik user.
     */
    public function getUserFavorites(int $userId): LengthAwarePaginator
    {
        return Favorite::with([
            'tutorial' => function ($q) {
                $q->with('category:id,name')
                  ->withCount(['comments', 'favorites']);
            },
        ])
            ->where('user_id', $userId)
            ->latest()
            ->paginate(10);
    }

    /**
     * Cek apakah user sudah mem-favorite tutorial tertentu.
     */
    public function isFavorited(int $userId, int $tutorialId): bool
    {
        return Favorite::where('user_id', $userId)
            ->where('tutorial_id', $tutorialId)
            ->exists();
    }
}
