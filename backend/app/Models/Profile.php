<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Profile extends Model
{
    protected $table = 'profile';

    protected  $primaryKey = 'id';

    protected $fillable = ['user_id', 'badge_id','phone', 'language', 'experience', 'address', 'banner_image', 'facebook', 'twitter', 'linkedin', 'instagram', 'metadescription', 'metatag', 'about', 'city', 'state', 'country', 'pincode', 'latitude', 'longitudes', 'address'];

    public static function boot() {
        parent::boot();
    }   

    // public function Spskill()
    // {
    //     return $this->hasMany('App\Models\Spskill','user_id');
    // }
    
    public function getCurrency()
    {
        return $this->hasOne(Currency::class, 'currency_id', 'id');
    }
}