<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['owner', 'content'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner', 'id');
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likable');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id', 'id');
    }

    public function commentsLimit()
    {
        return $this->hasMany(Comment::class, 'post_id', 'id')->latest()->limit(10);
    }
}
