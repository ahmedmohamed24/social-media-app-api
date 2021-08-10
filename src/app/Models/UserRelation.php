<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRelation extends Model
{
    use HasFactory;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['relatingUserID', 'relatedUserID', 'relation'];

    public function relatingUser()
    {
        return $this->belongsTo(User::class, 'relatingUserID', 'id');
    }

    public function relatedUser()
    {
        return $this->belongsTo(User::class, 'relatedUserID', 'id');
    }
}
