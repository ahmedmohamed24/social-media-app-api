<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['liked_by', 'likable_id', 'likable_type'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'liked_by', 'id');
    }

    public function likable()
    {
        return $this->morphTo();
    }
}
