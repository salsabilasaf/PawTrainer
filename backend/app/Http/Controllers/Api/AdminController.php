```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tutorial;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Favorite;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Dashboard Statistics
     */
    public function stats()
    {
        return response()->json([
            'success' => true,
            'message' => 'Dashboard statistics retrieved successfully',
            'data' => [
                'total_users'      => User::count(),
                'total_tutorials'  => Tutorial::count(),
                'total_categories' => Category::count(),
                'total_comments'   => Comment::count(),
                'total_favorites'  => Favorite::count(),
            ]
        ]);
    }

    /**
     * Recent Users
     */
    public function users(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        $users = User::latest()
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Users retrieved successfully',
            'data' => [
                'users' => $users->items(),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'last_page'    => $users->lastPage(),
                    'per_page'     => $users->perPage(),
                    'total'        => $users->total(),
                ]
            ]
        ]);
    }

    /**
     * Recent Comments
     */
    public function comments(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        $comments = Comment::with([
                'user:id,name,email',
                'tutorial:id,title'
            ])
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Comments retrieved successfully',
            'data' => [
                'comments' => $comments->items(),
                'pagination' => [
                    'current_page' => $comments->currentPage(),
                    'last_page'    => $comments->lastPage(),
                    'per_page'     => $comments->perPage(),
                    'total'        => $comments->total(),
                ]
            ]
        ]);
    }

    /**
     * Activity Log
     * (Versi sederhana tanpa tabel activity_logs)
     */
    public function activityLog()
    {
        $activities = [];

        $latestUsers = User::latest()
            ->take(3)
            ->get();

        foreach ($latestUsers as $user) {
            $activities[] = [
                'type' => 'register',
                'text' => "{$user->name} baru mendaftar",
                'created_at' => $user->created_at
            ];
        }

        $latestTutorials = Tutorial::latest()
            ->take(3)
            ->get();

        foreach ($latestTutorials as $tutorial) {
            $activities[] = [
                'type' => 'tutorial',
                'text' => "Tutorial '{$tutorial->title}' ditambahkan",
                'created_at' => $tutorial->created_at
            ];
        }

        $latestComments = Comment::latest()
            ->take(3)
            ->get();

        foreach ($latestComments as $comment) {
            $activities[] = [
                'type' => 'comment',
                'text' => "Komentar baru ditambahkan",
                'created_at' => $comment->created_at
            ];
        }

        usort($activities, function ($a, $b) {
            return strtotime($b['created_at'])
                - strtotime($a['created_at']);
        });

        return response()->json([
            'success' => true,
            'message' => 'Activity log retrieved successfully',
            'data' => [
                'logs' => array_slice($activities, 0, 10)
            ]
        ]);
    }
}
```
