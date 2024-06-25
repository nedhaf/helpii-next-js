<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\User;

class UserNotifications extends Model
{
    use HasFactory;

    protected $fillable = ['sender_user_id', 'receiver_user_id', 'notification_message', 'notification_type'];

    public function senderUserData()
    {
        return $this->hasOne(User::class, 'id', 'sender_user_id');
    }

    public function receiverUserData()
    {
        return $this->hasOne(User::class, 'id','receiver_user_id');
    }
}
