<?php

namespace App\Http\Controllers\Api\Gateway;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Services\ExternalApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExternalApiController extends Controller
{
    public function __construct(
        private readonly ExternalApiService $externalApiService
    ) {}

    /**
     * GET /api/gateway/breeds
     * Akses: admin, user
     * Ambil daftar ras kucing dari The Cat API.
     */
    public function catBreeds(): JsonResponse
    {
        $breeds = $this->externalApiService->getCatBreeds();

        return ResponseHelper::success('Daftar ras kucing berhasil diambil', [
            'source' => 'The Cat API (thecatapi.com)',
            'total'  => count($breeds),
            'breeds' => $breeds,
        ]);
    }

    /**
     * GET /api/gateway/facts
     * Akses: admin, user
     * Ambil fakta-fakta tentang kucing dari Cat Fact Ninja.
     *
     * Query: ?limit=10
     */
    public function catFacts(Request $request): JsonResponse
    {
        $limit = (int) $request->query('limit', 10);
        $data  = $this->externalApiService->getCatFacts($limit);

        return ResponseHelper::success('Fakta kucing berhasil diambil', [
            'source' => 'Cat Fact Ninja (catfact.ninja)',
            ...$data,
        ]);
    }

    /**
     * GET /api/gateway/videos/{keyword}
     * Akses: admin, user
     * Cari video YouTube berdasarkan keyword.
     *
     * Query: ?max_results=5
     */
    public function youtubeVideos(Request $request, string $keyword): JsonResponse
    {
        $maxResults = (int) $request->query('max_results', 5);
        $data       = $this->externalApiService->searchYouTubeVideos($keyword, $maxResults);

        return ResponseHelper::success('Video YouTube berhasil ditemukan', [
            'source' => 'YouTube Data API v3',
            ...$data,
        ]);
    }
}
