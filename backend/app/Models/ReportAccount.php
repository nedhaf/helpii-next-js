<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportAccount extends Model
{
    use HasFactory;

    protected $fillable = ['from_user_id', 'reported_user_id', 'comment'];

    public static function boot() {
        parent::boot();
    }
}
