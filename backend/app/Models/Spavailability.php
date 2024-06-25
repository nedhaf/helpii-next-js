<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Spavailability extends Model
{
    protected $table = 'sp_availability';

    protected  $primaryKey = 'id';

    protected $fillable = [
    	'user_id',
    	'timeslot',
    ];



    public static function boot()
    {
        parent::boot();

        
    }

    
}
