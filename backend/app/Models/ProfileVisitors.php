<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileVisitors extends Model
{
    use HasFactory;

    protected $fillable = ['visited_user_id', 'user_id'];
}
