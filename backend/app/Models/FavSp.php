<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class FavSp extends Model
{
    protected $table = 'fav_sp';

    protected  $primaryKey = 'id';

    protected $fillable = ['fav_user_id','user_id'];

    public static function boot()
    {
        parent::boot();

        
    }
    
}