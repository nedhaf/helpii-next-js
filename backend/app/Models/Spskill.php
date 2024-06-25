<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Spskill extends Model
{
    protected $table = 'sp_skill';

    protected  $primaryKey = 'id';

    protected $fillable = [
    	'user_id',
    	'skill_id',
    	'currency_id',
    	'description',
    	'metadescription',
        'metatitle',
    	'price_per_hour',
    	'price_per_day',
    	'show_price',
    	'offer_discount',
    	'offer_desc',
    	'offer_img',
    	'offer_start_date',
    	'offer_end_date',
        
        'city',
        'state',
        'country',
        'pincode',
        'latitude',
        'longitudes',
        'address',

    	'status',   	
    ];



    public static function boot()
    {
        parent::boot();

        
    }

    public function Skill()
    {
        return $this->belongsTo('App\Models\Skill');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\Currency');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\Auth\User');
    }

    public function profile()
    {
        return $this->belongsTo('App\Models\Profile','user_id');
    }

    public function getSkill()
    {
        return $this->hasMany('App\Models\Skill', 'id', 'skill_id');
    }
}
