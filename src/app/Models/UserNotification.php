<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    use HasFactory;
    protected $table = 'notification_devices';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'device_token', 'device_client'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
