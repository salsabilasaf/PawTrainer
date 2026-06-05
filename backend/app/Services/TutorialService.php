<?php

namespace App\Services;

use App\Models\Tutorial;
use App\Services\ExternalApiService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TutorialService
{
    /**
     * Ambil semua tutorial dengan filter opsional dan pagination.
     */
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $query = Tutorial::with(['category:id,name'])
            ->withCount(['comments', 'favorites']);

        // Filter by category
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Filter by difficulty
        if (!empty($filters['difficulty'])) {
            $query->where('difficulty', $filters['difficulty']);
        }

        // Search by title
        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        // Sort
        $sortBy    = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $allowedSorts = ['created_at', 'title', 'estimated_time', 'difficulty'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        return $query->paginate($filters['per_page'] ?? 10);
    }

    /**
     * Ambil satu tutorial by ID.
     */
    public function findById(int $id): Tutorial
    {
        return Tutorial::with([
            'category:id,name',
            'comments.user:id,name',
        ])
            ->withCount(['comments', 'favorites'])
            ->findOrFail($id);
    }

    /**
     * Buat tutorial baru. Hanya admin.
     */
    public function create(array $data): Tutorial
    {
        if (empty($data['image_url'])) {
            try {
                $data['image_url'] = app(ExternalApiService::class)->getRandomBreedImage() ?: null;
            } catch (\Exception $e) {
                // ignore and biarkan tetap null
            }
        }

        return Tutorial::create($data);
    }

    public function update(int $id, array $data): Tutorial
    {
        $tutorial = Tutorial::findOrFail($id);

        if (
            isset($data['title']) &&
            $data['title'] !== $tutorial->title
        ) {
            try {
                $youtube = app(\App\Services\ExternalApiService::class)
                    ->searchYouTubeVideos(
                        $data['title'] . ' cat training',
                        1
                    );

                if (!empty($youtube['videos'][0])) {
                    $data['youtube_url'] = $youtube['videos'][0]['url'];
                }
            } catch (\Exception $e) {
                // ignore
            }
        }

        if (empty($data['image_url'])) {
            try {
                $data['image_url'] = app(ExternalApiService::class)->getRandomBreedImage() ?: null;
            } catch (\Exception $e) {
                // ignore
            }
        }

        $tutorial->update($data);

        return $tutorial->fresh(['category:id,name']);
    }

    /**
     * Hapus tutorial by ID. Hanya admin.
     */
    public function delete(int $id): void
    {
        $tutorial = Tutorial::findOrFail($id);
        $tutorial->delete();
    }
}
