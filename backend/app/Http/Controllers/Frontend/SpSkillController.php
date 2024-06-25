<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Skill;
use App\Models\Spskill;
use App\Models\Auth\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SpSkillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $Spskill = new Spskill();
        $uid = Auth::user()->id;

        $my_skills = Spskill::where(['user_id' => $uid, 'status' => 1])->count();

        if($my_skills >= 4){
            return response()->json(
            [
                "status" => 403,
                'errors' => __('Oops! you have already created 4 skills if you want to create more than delete any one skill from your skills.'),
                "message" => null,
            ],200);
        }

        // Prepare data for inserting to table
        $tags = implode(', ', $request->tags);

        $Spskill->user_id       = $uid;
        $Spskill->skill_id      = $request->skill_id;
        $Spskill->description   = $request->description;
        $Spskill->tags          = $tags;
        $Spskill->show_price    = $request->show_price;
        $Spskill->price_per_hour= $request->price_per_hour != '' ? $request->price_per_hour : 0;
        $Spskill->price_per_day = $request->price_per_day != '' ? $request->price_per_day : 0;
        $Spskill->currency_id   = 2;
        $Spskill->status        = 1;
        $Spskill->city          = $request->city;
        $Spskill->state         = $request->state;
        $Spskill->country       = $request->country;
        $Spskill->pincode       = $request->pincode;
        $Spskill->latitude      = $request->latitude;
        $Spskill->longitudes    = $request->longitudes;
        $Spskill->address       = $request->address;

        if($request->skill_discount == 'yes'){
            $Spskill->offer_discount    = !empty($request->offer_discount) ? $request->offer_discount : NULL ;
            $Spskill->offer_desc        = !empty($request->offer_desc) ? $request->offer_desc : NULL;
            $Spskill->offer_start_date  = !empty($request->offer_start_date) ? $request->offer_start_date : NULL;
            $Spskill->offer_end_date    = !empty($request->offer_end_date) ? $request->offer_end_date : NULL;
        } else {
            $Spskill->offer_discount    = NULL;
            $Spskill->offer_desc        = NULL;
            $Spskill->offer_start_date  = NULL;
            $Spskill->offer_end_date    = NULL;
        }

        // return response()->json(
        // [
        //     'creatingSkill' => $Spskill,
        // ],200);

        if($Spskill->save()) {
            $message = __('Skill created successfully!');
            $errors = null;
        } else {
            $message = null;
            $errors = __('Skill not created!');
        }

        return response()->json(
        [
            'errors' => $errors,
            "status" => 200,
            "message" => $message,
        ],200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        //
        // $validator = Validator::make($request->all(), [
        //     'uid' => 'required|exists:sp_skill,user_id',
        //     'sps_id' => 'required|exists:sp_skill,id',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json(['errors' => $validator->errors()], 422);
        // }

        $getEditSpSkill = Spskill::where('id', $request->skill_id)->where('user_id',$request->uid)->first();

        if($getEditSpSkill) {
            $message = __('SpSkill found successfully!');
            $errors = '';
            $spskilldata = $getEditSpSkill;
        } else {
            $message = '';
            $errors = __('SpSkill not found!');
            $spskilldata = '';
        }
        return response()->json(
        [
            'errors' => $errors,
            "status" => 200,
            "message" => $message,
            "spskilldata" => $spskilldata
        ],200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $uid = Auth::user()->id;
        $sps_id = $request->skillId;
        $Spskill = Spskill::where('id',$sps_id)->where('user_id',$uid)->first();

         // Prepare data for inserting to table
        $tags = implode(', ', $request->tags);

        $Spskill->user_id       = $uid;
        $Spskill->skill_id      = $request->skill_id;
        $Spskill->description   = $request->description;
        $Spskill->tags          = $tags;
        $Spskill->show_price    = $request->show_price;
        $Spskill->price_per_hour= $request->price_per_hour != '' ? $request->price_per_hour : 0;
        $Spskill->price_per_day = $request->price_per_day != '' ? $request->price_per_day : 0;
        $Spskill->currency_id   = 2;
        $Spskill->status        = 1;
        $Spskill->city          = $request->city;
        $Spskill->state         = $request->state;
        $Spskill->country       = $request->country;
        $Spskill->pincode       = $request->pincode;
        $Spskill->latitude      = $request->latitude;
        $Spskill->longitudes    = $request->longitudes;
        $Spskill->address       = $request->address;

        if($request->skill_discount == 'yes'){
            $Spskill->offer_discount    = !empty($request->offer_discount) ? $request->offer_discount : NULL ;
            $Spskill->offer_desc        = !empty($request->offer_desc) ? $request->offer_desc : NULL;
            $Spskill->offer_start_date  = !empty($request->offer_start_date) ? $request->offer_start_date : NULL;
            $Spskill->offer_end_date    = !empty($request->offer_end_date) ? $request->offer_end_date : NULL;
        } else {
            $Spskill->offer_discount    = NULL;
            $Spskill->offer_desc        = NULL;
            $Spskill->offer_start_date  = NULL;
            $Spskill->offer_end_date    = NULL;
        }

        if($Spskill->save()) {
            $message = __('Skill updated successfully!');
            $errors = null;
        } else {
            $message = null;
            $errors = __('Skill not update!');
        }

        return response()->json(
        [
            'errors' => $errors,
            "status" => 200,
            "message" => $message,
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'skill_id' => 'required|exists:sp_skill,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()->get('skill_id')], 422);
        }

        // if ($validator->fails()) {
        //     return response()->json(['errors' => [
        //         'skill_id' => $validator->messages()->get('skill_id') ? $validator->messages()->get('skill_id') : null,
        //         'uid' => $validator->messages()->get('uid') ? $validator->messages()->get('uid') : null,
        //     ]], 422);
        // }
        $uid = Auth::user()->id;
        $Spskill = Spskill::where('id',$request->skill_id)->where('user_id',$uid)->firstOrFail();

        if($Spskill->delete()) {
            $message = __('Spskill deleted successfully!');
            $errors = null;
        } else {
            $message = null;
            $errors = __('Spskill are not deleted!');
        }

        return response()->json(
        [
            'errors' => $errors,
            "status" => 200,
            "message" => $message,
        ],200);
    }
}
