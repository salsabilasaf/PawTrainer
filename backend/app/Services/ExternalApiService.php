<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ExternalApiService
{
    // ─── The Cat API ──────────────────────────────────────────────────────────

    /**
     * Ambil daftar ras kucing dari The Cat API.
     * Cache 1 jam karena data jarang berubah.
     */
    public function getCatBreeds(): array
    {
        return Cache::remember('cat_breeds', 3600, function () {
            $response = Http::withHeaders([
                'x-api-key' => config('services.thecatapi.key', ''),
            ])->get('https://api.thecatapi.com/v1/breeds');

            if (!$response->successful()) {
                throw new \Exception('Gagal mengambil data dari The Cat API: ' . $response->status(), 502);
            }

            $breeds = $response->json();

            // Map ke format yang lebih bersih
            return array_map(fn($breed) => [
                'id'          => $breed['id'],
                'name'        => $breed['name'],
                'origin'      => $breed['origin'] ?? null,
                'temperament' => $breed['temperament'] ?? null,
                'description' => $breed['description'] ?? null,
                'life_span'   => $breed['life_span'] ?? null,
                'weight_kg'   => $breed['weight']['metric'] ?? null,
                'wikipedia'   => $breed['wikipedia_url'] ?? null,
                            'image_url'   => $breed['image']['url'] ?? null,
            ], $breeds);
        });
    }

    // ─── Cat Fact Ninja ───────────────────────────────────────────────────────

    /**
     * Ambil fakta-fakta tentang kucing.
     * Cache 30 menit.
     *
     * @param  int  $limit  Jumlah fakta yang diminta (1-100)
     */
    public function getCatFacts(int $limit = 10): array
    {
        $limit = min(max($limit, 1), 100); // Clamp antara 1–100

        $cacheKey = "cat_facts_{$limit}";

        return Cache::remember($cacheKey, 1800, function () use ($limit) {
            $response = Http::get('https://catfact.ninja/facts', [
                'limit' => $limit,
            ]);

            if (!$response->successful()) {
                throw new \Exception('Gagal mengambil data dari Cat Fact Ninja: ' . $response->status(), 502);
            }

            $json = $response->json();

            return [
                'facts'        => $json['data'] ?? [],
                'total'        => $json['total'] ?? 0,
                'per_page'     => $json['per_page'] ?? $limit,
                'current_page' => $json['current_page'] ?? 1,
            ];
        });
    }

    // ─── YouTube Data API ─────────────────────────────────────────────────────

    /**
     * Cari video YouTube berdasarkan keyword.
     * Cache 15 menit per keyword.
     *
     * @param  string  $keyword  Kata kunci pencarian
     * @param  int     $maxResults  Jumlah hasil (1-50)
     */
    public function searchYouTubeVideos(string $keyword, int $maxResults = 5): array
    {
        $apiKey = config('services.youtube.key');

        if (empty($apiKey)) {
            throw new \Exception('YouTube API Key belum dikonfigurasi. Tambahkan YOUTUBE_API_KEY di .env', 503);
        }

        $cacheKey = 'youtube_' . md5($keyword . $maxResults);

        return Cache::remember($cacheKey, 900, function () use ($keyword, $maxResults, $apiKey) {
            $response = Http::get('https://www.googleapis.com/youtube/v3/search', [
                'part'       => 'snippet',
                'q'          => $keyword,
                'type'       => 'video',
                'maxResults' => min(max($maxResults, 1), 50),
                'key'        => $apiKey,
            ]);

            if ($response->status() === 403) {
                throw new \Exception('YouTube API Key tidak valid atau kuota habis.', 403);
            }

            if (!$response->successful()) {
                throw new \Exception('Gagal mengambil data dari YouTube API: ' . $response->status(), 502);
            }

            $json  = $response->json();
            $items = $json['items'] ?? [];

            // Map ke format yang lebih bersih
            return [
                'keyword'      => $keyword,
                'total_results' => $json['pageInfo']['totalResults'] ?? 0,
                'videos'       => array_map(fn($item) => [
                    'video_id'     => $item['id']['videoId'],
                    'title'        => $item['snippet']['title'],
                    'description'  => $item['snippet']['description'],
                    'channel'      => $item['snippet']['channelTitle'],
                    'published_at' => $item['snippet']['publishedAt'],
                    'thumbnail'    => $item['snippet']['thumbnails']['medium']['url'] ?? null,
                    'url'          => 'https://www.youtube.com/watch?v=' . $item['id']['videoId'],
                ], $items),
            ];
        });
    }

    /**
     * Ambil satu random breed image dari The Cat API.
     */
    public function getRandomBreedImage(): ?string
    {
        try {
            $breeds = $this->getCatBreeds();
            $breedsWithImage = array_filter($breeds, fn($b) => !empty($b['image_url']));

            if (empty($breedsWithImage)) {
                return null;
            }

            $random = $breedsWithImage[array_rand($breedsWithImage)];
            return $random['image_url'] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }
}

