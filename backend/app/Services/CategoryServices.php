<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    /**
     * Ambil semua kategori beserta jumlah tutorial-nya.
     */
    public function getAll(): Collection
    {
        return Category::withCount('tutorials')
            ->orderBy('name')
            ->get();
    }

    /**
     * Ambil satu kategori by ID.
     */
    public function findById(int $id): Category
    {
        return Category::withCount('tutorials')->findOrFail($id);
    }

    /**
     * Buat kategori baru. Hanya admin.
     */
    public function create(array $data): Category
    {
        return Category::create($data);
    }

    /**
     * Update kategori. Hanya admin.
     */
    public function update(int $id, array $data): Category
    {
        $category = Category::findOrFail($id);
        $category->update($data);
        return $category->fresh();
    }

    /**
     * Hapus kategori. Hanya admin.
     * Akan gagal jika masih ada tutorial yang menggunakan kategori ini (FK constraint).
     */
    public function delete(int $id): void
    {
        $category = Category::findOrFail($id);

        if ($category->tutorials()->exists()) {
            throw new \Exception('Kategori tidak dapat dihapus karena masih memiliki tutorial terkait.', 409);
        }

        $category->delete();
    }
}
