<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'bio', 'phone_number', 'phone_verified_at', 'country', 'city', 'postal-code', 'address-line-1', 'address-line-2', 'photo_path', 'cover_path', 'education'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
