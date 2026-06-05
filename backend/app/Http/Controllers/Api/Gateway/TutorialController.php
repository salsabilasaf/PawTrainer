<?php

namespace App\Http\Controllers\Api\Gateway;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tutorial\StoreTutorialRequest;
use App\Http\Requests\Tutorial\UpdateTutorialRequest;
use App\Services\TutorialService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TutorialController extends Controller
{
    public function __construct(
        private readonly TutorialService $tutorialService
    ) {}

    /**
     * GET /api/gateway/tutorials
     * Akses: admin, user (semua yang sudah login)
     *
     * Query params opsional:
     *   ?category_id=1
     *   ?difficulty=beginner
     *   ?search=kucing
     *   ?sort_by=created_at&sort_order=desc
     *   ?per_page=10
     */
    public function index(Request $request): JsonResponse
    {
        $tutorials = $this->tutorialService->getAll($request->query());

        return ResponseHelper::success('Daftar tutorial berhasil diambil', [
            'tutorials'  => $tutorials->items(),
            'pagination' => [
                'current_page' => $tutorials->currentPage(),
                'per_page'     => $tutorials->perPage(),
                'total'        => $tutorials->total(),
                'last_page'    => $tutorials->lastPage(),
            ],
        ]);
    }

    /**
     * GET /api/gateway/tutorials/{id}
     * Akses: admin, user
     */
    public function show(int $id): JsonResponse
    {
        $tutorial = $this->tutorialService->findById($id);

        return ResponseHelper::success('Detail tutorial berhasil diambil', [
            'tutorial' => $tutorial,
        ]);
    }

    /**
     * POST /api/gateway/tutorials
     * Akses: admin only
     */
    public function store(StoreTutorialRequest $request): JsonResponse
    {
        $tutorial = $this->tutorialService->create($request->validated());

        return ResponseHelper::created('Tutorial berhasil dibuat', [
            'tutorial' => $tutorial->load('category:id,name'),
        ]);
    }
    

    /**
     * PUT /api/gateway/tutorials/{id}
     * Akses: admin only
     */
    public function update(UpdateTutorialRequest $request, int|string $id): JsonResponse
    {
        $tutorial = $this->tutorialService->update((int) $id, $request->validated());

        return ResponseHelper::success('Tutorial berhasil diperbarui', [
            'tutorial' => $tutorial,
        ]);
    }

    /**
     * DELETE /api/gateway/tutorials/{id}
     * Akses: admin only
     */
    public function destroy(int $id): JsonResponse
    {
        // Authorize admin
        if (auth()->user()->role !== 'admin') {
            return ResponseHelper::forbidden('Hanya admin yang dapat menghapus tutorial.');
        }

        $this->tutorialService->delete($id);

        return ResponseHelper::success('Tutorial berhasil dihapus');
    }
}
