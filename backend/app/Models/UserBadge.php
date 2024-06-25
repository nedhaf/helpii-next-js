<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBadge extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'badge_name', 'image', 'status'];

    // Get all badges
    public static function allbadge() {
        $skills = static::where('status', 1)->get();
        $selection = array();
        $items = array();

        foreach ($skills as $key => $badge) {
            $selection['id']=$badge->id;
            $selection['badge_name']=$badge->badge_name;
            $selection['img']=$badge->image;
            $items[]=$selection;

        }
        return $items;
    }
}
