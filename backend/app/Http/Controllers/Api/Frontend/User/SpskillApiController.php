<?php

namespace App\Http\Controllers\Api\Frontend\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\Spskill;
use App\Models\Skill;
use App\Models\Currency;
use App\Models\Feedback;

use Illuminate\Support\Facades\DB;

use App\Models\Auth\User;
use App\Models\Auth\SocialAccount;
use App\Models\UserAds;
use Config;
use Illuminate\Support\Facades\Validator;


class SpskillApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //"skill_id" => "14"
        // "description" => "Test"
        // "tags" => "Test tersse"
        // "price_per_hour" => "12"
        // "price_per_day" => null
        // "show_price" => "hour"
        // "status" => "1"
        // "city" => null
        // "state" => null
        // "country" => null
        // "pincode" => null
        // "latitude" => null
        // "longitudes" => null
        // "address" => "ahmedabad"
        // "route" => null
        // "currency_id" => "2"
        // if(Auth::guard('sanctum')->guest()){
        //     // Token is not valid
        //     return response()->json(['message' => 'Unauthorized'], 401);
        // }
        // $token = $request->bearerToken();

        // Validate request data
        $validator = Validator::make($request->all(), [
            'uid' => 'required|exists:users,id',
            'skill_id' => 'required|exists:skill,id',
            'description' => 'required',
            'show_price' => 'required|in:day,hour,both',
            'price_per_day' => function ($attribute, $value, $fail) use ($request) {
                if ($request->show_price === 'day' || $request->show_price === 'both') {
                    if (!$value) {
                        $fail('Price per Day is required when show_price is Day or Both.');
                    }
                }
            },
            'price_per_hour' => function ($attribute, $value, $fail) use ($request) {
                if ($request->show_price === 'hour' || $request->show_price === 'both') {
                    if (!$value) {
                        $fail('Price per Hour is required when show_price is Hour or Both.');
                    }
                }
            },
            'skill_discount' => 'in:yes,no',
            'offer_discount' => ($request->skill_discount == 'yes') ? 'required|numeric|min:1|max:99.99' : '',
            'offer_start_date' => ($request->skill_discount == 'yes') ? 'required|date' : '',
            'offer_end_date' => ($request->skill_discount == 'yes') ? 'required|date|after:offer_start_date' : '',
            'address' => 'required',
            'pincode' => 'required',
            'latitude' => 'required',
            'longitudes' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $Spskill = new Spskill();
        $uid = $request->uid;
        // To check current users total skills.
        $my_skills = Spskill::where(['user_id' => $uid, 'status' => 1])->get();
        if($my_skills->count() >= (int)config('app.APP_SKILL_LIMIT') && $request->status == 1){
            return response()->json(
            [
                'errors' => __('strings.new.skill_more_than_4'),
                "status" => 200,
                "message" => array(),
            ],200);
        }
        // Prepare data for inserting to table
        $Spskill->user_id       = $uid;
        $Spskill->skill_id      = $request->skill_id;
        $Spskill->description   = $request->description;
        $Spskill->tags          = $request->tags;
        $Spskill->show_price    = $request->show_price;
        $Spskill->price_per_hour= $request->price_per_hour != '' ? $request->price_per_hour : 0;
        $Spskill->price_per_day = $request->price_per_day != '' ? $request->price_per_day : 0;
        $Spskill->currency_id   = ($request->currency_id) ? $request->currency_id : 0;
        $Spskill->status        = ($request->status) ? $request->status : 0;
        $Spskill->city          = $request->city;
        $Spskill->state         = $request->state;
        $Spskill->country       = $request->country;
        $Spskill->pincode       = $request->pincode;
        $Spskill->latitude      = $request->latitude;
        $Spskill->longitudes    = $request->longitudes;
        $Spskill->address       = $request->address;

        if($request->skill_discount == 'yes'){
            $Spskill->offer_discount    = $request->offer_discount;
            $Spskill->offer_desc        = $request->offer_desc;
            $Spskill->offer_start_date  = $request->offer_start_date;
            $Spskill->offer_end_date    = $request->offer_end_date;
        } else {
            $Spskill->offer_discount    = NULL;
            $Spskill->offer_desc        = NULL;
            $Spskill->offer_start_date  = NULL;
            $Spskill->offer_end_date    = NULL;
        }

        if($Spskill->save()) {
            if(!empty($request->offer_img)){
                $dir = public_path().'/storage/spskills/';
                $fileimage = $request->file('offer_img');
                $fileimage->move($dir, $fileName);
            }
            $message = array('message' => __('strings.new.skill_insert_message'));
            $errors = array();
        } else {
            $message = array();
            $errors = array('message' => __('Skill not created!'));
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
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'uid' => 'required|exists:sp_skill,user_id',
            'sps_id' => 'required|exists:sp_skill,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $getEditSpSkill = Spskill::where('id', $request->sps_id)->where('user_id',$request->uid)->firstOrFail();
        if($getEditSpSkill) {
            $message = 'SpSkill found successfully!';
            $errors = '';
            $spskilldata = $getEditSpSkill;
        } else {
            $message = '';
            $errors = 'SpSkill not found!';
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'uid' => 'required|exists:sp_skill,user_id',
            'sps_id' => 'required|exists:sp_skill,id',
            'skill_id' => 'required|exists:skill,id',
            'description' => 'required',
            'show_price' => 'required|in:day,hour,both',
            'price_per_day' => function ($attribute, $value, $fail) use ($request) {
                if ($request->show_price === 'day' || $request->show_price === 'both') {
                    if (!$value) {
                        $fail('Price per Day is required when show_price is Day or Both.');
                    }
                }
            },
            'price_per_hour' => function ($attribute, $value, $fail) use ($request) {
                if ($request->show_price === 'hour' || $request->show_price === 'both') {
                    if (!$value) {
                        $fail('Price per Hour is required when show_price is Hour or Both.');
                    }
                }
            },
            'skill_discount' => 'in:yes,no',
            'offer_discount' => ($request->skill_discount == 'yes') ? 'required|numeric|min:1|max:99.99' : '',
            'offer_start_date' => ($request->skill_discount == 'yes') ? 'required|date' : '',
            'offer_end_date' => ($request->skill_discount == 'yes') ? 'required|date|after:offer_start_date' : '',
            'address' => 'required',
            'pincode' => 'required',
            'latitude' => 'required',
            'longitudes' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $uid = $request->uid;
        $sps_id = $request->sps_id;
        $Spskill = Spskill::where('id',$sps_id)->where('user_id',$uid)->firstOrFail();
        if($Spskill->status != 1){
            $my_skills = Spskill::where(['user_id' => Auth::user()->id, 'status' => 1])->get();
            if($my_skills->count() >= (int)config('app.APP_SKILL_LIMIT') && $request->get('status') == 1){
                return response()->json(array('success' => [], 'errors' => ['message' => __('strings.new.skill_more_than_4')]));
            }
        }

        $Spskill->user_id       = $uid;
        $Spskill->skill_id      = $request->skill_id;
        $Spskill->description   = $request->description;
        $Spskill->tags          = $request->tags;
        $Spskill->show_price    = $request->show_price;
        $Spskill->price_per_hour= $request->price_per_hour != '' ? $request->price_per_hour : 0;
        $Spskill->price_per_day = $request->price_per_day != '' ? $request->price_per_day : 0;
        $Spskill->currency_id   = ($request->currency_id) ? $request->currency_id : 0;
        $Spskill->status        = ($request->status) ? $request->status : 0;
        $Spskill->city          = $request->city;
        $Spskill->state         = $request->state;
        $Spskill->country       = $request->country;
        $Spskill->pincode       = $request->pincode;
        $Spskill->latitude      = $request->latitude;
        $Spskill->longitudes    = $request->longitudes;
        $Spskill->address       = $request->address;

        if($request->skill_discount == 'yes'){
            $Spskill->offer_discount    = $request->offer_discount;
            $Spskill->offer_desc        = $request->offer_desc;
            $Spskill->offer_start_date  = $request->offer_start_date;
            $Spskill->offer_end_date    = $request->offer_end_date;
        } else {
            $Spskill->offer_discount    = NULL;
            $Spskill->offer_desc        = NULL;
            $Spskill->offer_start_date  = NULL;
            $Spskill->offer_end_date    = NULL;
            if(!empty($Spskill->offer_img)){
                $offer_img ="storage/spskills/".$Spskill->offer_img;
                if(file_exists($offer_img))
                    unlink(public_path($offer_img));
            }
            $Spskill->offer_img = NULL;
        }

        if($Spskill->save()) {
            if(!empty($request->offer_img)){
                $dir = public_path().'/storage/spskills/';
                $fileimage = $request->file('offer_img');
                $fileimage->move($dir, $fileName);
            }
            $message = array('message' => __('strings.new.skill_update_message'));
            $errors = array();
        } else {
            $message = array();
            $errors = array('message' => __('Skill not updated!'));
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
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'uid' => 'required|exists:sp_skill,user_id',
            'sps_id' => 'required|exists:sp_skill,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $Spskill = Spskill::where('id',$request->sps_id)->where('user_id',$request->uid)->firstOrFail();

        if($Spskill->delete()) {
            $message = array('message' => 'Spskill deleted successfully!');
            $errors = array();
        } else {
            $message = array();
            $errors = array('message' => 'Spskill are not deleted!');
        }

        return response()->json(
        [
            'errors' => $errors,
            "status" => 200,
            "message" => $message,
        ],200);

    }
}
