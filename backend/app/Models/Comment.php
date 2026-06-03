<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tutorial_id',
        'parent_id',
        'comment',
    ];

    // ─── Relations ────────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tutorial()
    {
        return $this->belongsTo(Tutorial::class);
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->with('user');
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }
}