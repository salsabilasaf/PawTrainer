<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Favorite;
use App\Models\Tutorial;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function stats(): JsonResponse
    {
        return ResponseHelper::success('Statistik dashboard berhasil diambil', [
            'total_users' => User::count(),
            'total_tutorials' => Tutorial::count(),
            'total_categories' => Category::count(),
            'total_comments' => Comment::count(),
            'total_favorites' => Favorite::count(),
        ]);
    }

    public function users(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 10), 100);

        $users = User::select('id', 'name', 'email', 'role', 'created_at')
            ->latest()
            ->paginate($perPage);

        return ResponseHelper::success('Daftar user berhasil diambil', [
            'users' => $users->items(),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'last_page' => $users->lastPage(),
            ],
        ]);
    }

    public function updateUser(Request $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255|unique:users,email,' . $user->id,
            'role' => 'sometimes|required|in:admin,user',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        $user->update($validator->validated());

        return ResponseHelper::success('User berhasil diperbarui', [
            'user' => $user->only(['id', 'name', 'email', 'role', 'created_at']),
        ]);
    }

    public function destroyUser(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return ResponseHelper::forbidden('Admin tidak bisa menghapus akun sendiri.');
        }

        $user->delete();

        return ResponseHelper::success('User berhasil dihapus');
    }

    public function comments(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 10), 100);

        $comments = Comment::with([
            'user:id,name,email',
            'tutorial:id,title',
        ])
            ->latest()
            ->paginate($perPage);

        return ResponseHelper::success('Daftar komentar berhasil diambil', [
            'comments' => $comments->items(),
            'pagination' => [
                'current_page' => $comments->currentPage(),
                'per_page' => $comments->perPage(),
                'total' => $comments->total(),
                'last_page' => $comments->lastPage(),
            ],
        ]);
    }

    public function activityLog(): JsonResponse
    {
        $activities = [];

        foreach (User::latest()->take(3)->get() as $user) {
            $activities[] = [
                'type' => 'register',
                'text' => "{$user->name} baru mendaftar",
                'created_at' => $user->created_at,
            ];
        }

        foreach (Tutorial::latest()->take(3)->get() as $tutorial) {
            $activities[] = [
                'type' => 'tutorial',
                'text' => "Tutorial '{$tutorial->title}' ditambahkan",
                'created_at' => $tutorial->created_at,
            ];
        }

        foreach (Comment::latest()->take(3)->get() as $comment) {
            $activities[] = [
                'type' => 'comment',
                'text' => 'Komentar baru ditambahkan',
                'created_at' => $comment->created_at,
            ];
        }

        usort($activities, fn ($a, $b) => strtotime($b['created_at']) <=> strtotime($a['created_at']));

        return ResponseHelper::success('Activity log berhasil diambil', [
            'logs' => array_slice($activities, 0, 10),
        ]);
    }
}
