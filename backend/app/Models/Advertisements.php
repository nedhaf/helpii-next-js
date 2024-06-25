<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisements extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','skill_id', 'title', 'phone','link','image', 'description', 'color', 'badge_img', 'city', 'start_date', 'end_date', 'position', 'cost', 'isFront', 'show_in_front_profile', 'show_in_front_ads', 'status'];

    public function getSkill()
    {
        return $this->hasOne(Skill::class, 'id', 'skill_id');
    }
}
