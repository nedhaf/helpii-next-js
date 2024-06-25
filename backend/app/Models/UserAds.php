<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\User;

class UserAds extends Model {
    
    protected $table = 'user_ads';

    protected  $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'skill_id',
        'currency_id',
        'title',
        'description',
        'price_per_hour',
        'price_per_day',
        'show_price',
        'metadescription',
        'metatags',
        'metatitle',
        'address',
        'city',
        'state',
        'country',
        'pincode',
        'latitude',
        'longitudes',
        'status'
    ];
    
    public static function getUserNameById($user_id){
        if($user_id > 0){
            $user = User::where('id', $user_id)->first();
            return $user->first_name.' '.$user->last_name;
        }
        return '';
    }

    public function getCurrency(){
        return $this->hasOne(Currency::class, 'id', 'currency_id');
    }

    public function getAdsSkill(){
        return $this->hasOne(Skill::class, 'id', 'skill_id');
    }
}
