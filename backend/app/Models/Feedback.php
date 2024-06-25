<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Spskill;
use Illuminate\Support\Facades\DB;
use App\Models\Skill;
use App\Models\Auth\User;


class Feedback extends Model
{
    protected $table = 'rating';

    protected  $primaryKey = 'id';

    protected $fillable = ['from_userid','to_userid','value_for_money','quality_of_work','relation_with_customer','performance','total','review', 'user_liked', 'user_disliked'];

    public static function boot() {
        parent::boot();
    }

    public static function SpRemainigSkill($touserid,$fromuserId){

        $all_feedskills=array();
        $skills = array();

        $feedbackskills = static::where('from_userid', $fromuserId)->where('to_userid', $touserid)->get();


        if(!empty($feedbackskills))
        {
            foreach ($feedbackskills as $feedskills) {
                $all_feedskills[]=$feedskills->sp_skill_id;            
            } 

            if(!empty($all_feedskills))
            {
                $remainingSkills = Spskill::whereNotIn('skill_id', $all_feedskills)->where('status', 1)->where('user_id', $touserid)->get();                
                if(!empty($remainingSkills))
                {
                    $skills = $remainingSkills;
                }
                else
                {
                    $skills = Spskill::where('user_id','=',$touserid)->where('status','=',1)->get();
                }
            }
            else
            {
                $skills = Spskill::where('user_id','=',$touserid)->where('status','=',1)->get();
            }
        }
        else
        {
            $skills = Spskill::where('user_id','=',$touserid)->where('status','=',1)->get();
        }

           
        return $skills;

    }


    public static function CheckRemainigSkill($touserid,$fromuserId){

        $all_feedskills=array();
        $skills = array();

        $feedbackskills = static::where('from_userid', $fromuserId)->where('to_userid', $touserid)->get();


        if(!empty($feedbackskills))
        {
            foreach ($feedbackskills as $feedskills) {
                $all_feedskills[]=$feedskills->sp_skill_id;            
            } 

            if(!empty($all_feedskills))
            {
                $remainingSkills = Spskill::whereNotIn('skill_id', $all_feedskills)->where('status', 1)->where('user_id', $touserid)->get(); 
                $remainingSkills = reset($remainingSkills);
                
                if(!empty($remainingSkills))
                {
                    return 1;
                }
                else
                {
                    //echo "2";die;
                    return 0;
                }
            }
            else
            {
                //echo "3";die;
                return 1;
            }
        }
        else
        {
            //echo "4";die;
            return 1;
        }

    }

    public static function user_average_rating($userid)
    {
        $userfeedbackAverage = static::where('to_userid', $userid)->avg('total');
        if($userfeedbackAverage)
        {
            return $userfeedbackAverage;
        }
        else
        {
            return 0;
        }
    }

    public function Skill()
    {
        return $this->hasOne(Skill::class, 'id', 'sp_skill_id');
    }

    public function SpSkill()
    {
        return $this->hasOne(Spskill::class, 'id', 'sp_skill_id');
    }
    
}
