<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['commented_by',  'content', 'post_id'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'commented_by', 'id');
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likable');
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }

    public function replies()
    {
        return $this->hasMany(Reply::class, 'comment_id', 'id');
    }
}
