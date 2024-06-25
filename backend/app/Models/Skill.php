<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class Skill extends Model
{
    use HasFactory, SoftDeletes;

    protected $guards = ['web'];

    protected $table = 'skill';
    /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
    protected $fillable = [
        "id",
        "name",
        "description",
        "avatar",
        "status"
    ];

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    public static function GetAll() {
        $skills = static::where('status', 1)->get();
        $selection = array();
        $selection[""]=__('strings.new.select_skill');
        foreach ($skills as $key => $value) {
            $selection[$value->id]=$value->name;
        }
        return $selection;
    }

    public static function getSkillNameById($id){
        if($id > 0){
            $record = static::where(['status' => 1, 'id' => $id])->first();
            return $record->name;
        }
        return '';
    }

    public static function autocomplete($query) {
        //die($query);
        $skills = static::where('status', 1)->where('name','like',$query.'%')->get();
        $selection = array();
        $items = array();

        foreach ($skills as $key => $value) {
            $selection['id']=$value->id;
            $selection['name']=$value->name;
            $items[]=$selection;

        }
        return $items;
    }

    // Get all skills
    public static function allskill() {
        $skills = static::where('status', 1)->get();
        $selection = array();
        $items = array();

        foreach ($skills as $key => $value) {
            $selection['id']=$value->id;
            $selection['name']=$value->name;
            $selection['img']=$value->avatar;
            $items[]=$selection;

        }
        return $items;
    }

    public static function SpRemainigSkill($userid,$skillid){

        $all_spskills=array();
        $skills = array();

        $spskills = DB::table('sp_skill')
                    ->select('sp_skill.skill_id')
                    ->where('sp_skill.user_id', $userid)
                    ->where('sp_skill.status', 1)
                    ->get();


        if(!empty($spskills))
        {
            foreach ($spskills as $spskill) {
                if($skillid != $spskill->skill_id)
                {
                    $all_spskills[]=$spskill->skill_id;
                }
            }


            if(!empty($all_spskills))
            {
                $remainingSkills = static::whereNotIn('id', $all_spskills)->where('status', 1)->get();
                if(!empty($remainingSkills))
                {
                    $skills = $remainingSkills;
                }
                else
                {
                    $skills = static::where('status', 1)->get();
                }
            }
            else
            {
                $skills = static::where('status', 1)->get();
            }
        }
        else
        {
            $skills = static::where('status', 1)->get();
        }

        //$skills = static::where('status', 1)->get();
        $selection = array();
        $selection[""]=__('strings.new.select_skill');
        if(!empty($skills))
        {
            foreach ($skills as $key => $value) {
                $selection[$value->id]=$value->name;
            }
        }
        return $selection;

    }

    public static function allSkills() {
        //die($query);
        $skills = static::where('status', 1)->get();
        $selection = array();
        $items = array();

        foreach ($skills as $key => $value) {
            $selection['id']=$value->id;
            $selection['name']=$value->name;
            // $selection['code']=$value->id;  // change by bindiya
            $items[]=$selection;

        }
        return $items;
    }
}
