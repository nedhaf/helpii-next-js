<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Skill;
use Illuminate\Support\Facades\Storage;
use App\Models\Spskill;
use App\Models\Currency;
use App\Models\Feedback;
use App\Models\OverallProfileRating;
use Illuminate\Support\Facades\DB;
use App\Models\Auth\User;
use App\Models\Auth\SocialAccount;
use App\Models\UserAds;
use Config;
use App\Models\Advertisements;
use App\Models\ProfileVisitors;

class CommonApiController extends Controller
{
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
        //
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
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Update device token for authenticated user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateDeviceToke(Request $request)
    {
        $user_id = auth('sanctum')->id();
        $device_token = $request->device_token;

        $updateUser = User::where('id', $user_id)->firstOrFail();

        $updateUser->device_token = $device_token;

        $updateResult = $updateUser->save();

        if( $updateResult ) {
            return response()->json(
            [
                "status" => 200,
                'errors' => '',
                "message" => "Device token updated.",
                'results' => $updateUser
            ],200);
        } else {
            return response()->json(
            [
                "status" => 200,
                'errors' => '',
                "message" => "Device token not updated.",
                'results' => ""
            ],200);
        }
    }

     /**
     * Seach Ads .
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function searchspAdsApp(Request $request)
    {
        if($request->search_for == 'ads') {
            return response()->json($this->getUserAdsResults($request, 40));
        }

    }

    /**
     * Seach Profile .
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function searchspProfileApp(Request $request)
    {
        if($request->search_for == 'profile') {
            return response()->json($this->getSearchResults($request, 40));
        }

    }

    public function getUserAdsResults($request, $per_page_records) {
        // dd($request);
        if( !empty($request->skillid) || $request->skillid != '' ) {
            $skillid    = $request->skillid;
            $all_tag    = json_decode($skillid);
        } else {
            $allSkills = Skill::all();
            $newSkillsId = array();
            foreach( $allSkills as $skeys => $skill ) {
                $newSkillsId[] = $skill;
            }
            $skillid = $newSkillsId;
            $all_tag = $skillid;
        }

        $setting = [];
        $results = [];
        $lng    = $request->lng;
        $lat    = $request->lat;
        $location['latitude']=$lat;
        $location['longitude']=$lng;

        if(!empty($all_tag) && $all_tag!="null"){
            foreach ($all_tag as $key => $value) {
                $user_ads = DB::table('user_ads');
                $user_ads->where('status', 1);
                $user_ads->where(function ($main_query) use($value){
                    $main_query->whereIn('skill_id', function($query) use($value){
                        $query->select('id')->from('skill')->where('name', $value->name);
                    });
                });
                $user_ads->whereIn('user_id', function($query){
                    $query->select('id')->from('users')->where('is_sp', 1)->where('active', 1);
                });

                if($user_ads->paginate()->count() == 0) {

                    $user_ads_new = DB::table('user_ads');
                    $user_ads_new->join('profile', function ($join) {
                        $join->on('user_ads.user_id', '=', 'profile.user_id');
                    });
                    $user_ads_new->where('status', 1);

                    $user_ads_new->where(function ($main_query) use($value){
                        $main_query->whereIn('skill_id', function($query) use($value){
                            $query->select('id')->from('skill')->where('name', $value->name);
                        });
                    });
                    $user_ads_new->whereIn('user_ads.user_id', function($query){
                        $query->select('id')->from('users')->where('is_sp', 1)->where('active', 1);
                    });
                    // dd($user_ads_new);
                     //dd($user_ads_new->paginate($per_page_records)); exit;
                    if(empty($location['latitude']) && empty($location['longitude'])){
                        $haversine = "(6371 * acos(cos(radians(latitude)) * cos(radians(longitudes)) + sin(radians(latitude))))";
                        $user_ads_new->whereRaw("{$haversine} < ?", [60]);
                    } else {

                        $haversine = "(6371 * acos(cos(radians(".$location['latitude'].")) * cos(radians(latitude)) * cos(radians(longitudes) - radians(".$location['longitude'].")) + sin(radians(".$location['latitude'].")) * sin(radians(latitude))))";
                    }
                    // $haversine = "(6371 * acos(cos(radians(".$location['latitude'].")) * cos(radians(latitude)) * cos(radians(longitudes) - radians(".$location['longitude'].")) + sin(radians(".$location['latitude'].")) * sin(radians(latitude))))";
                    // echo "<pre>";print_r($haversine);echo "</pre>";exit;

                    $results[] = $user_ads_new->paginate($per_page_records);
                } else {

                    if(empty($location['latitude']) && empty($location['longitude'])){
                        // $haversine = "(6371 * acos(cos(radians(latitude)) * cos(radians(longitudes)) + sin(radians(latitude))))";
                        $user_ads->whereRaw("1");
                    } else {
                        $haversine = "(6371 * acos(cos(radians(".$location['latitude'].")) * cos(radians(latitude)) * cos(radians(longitudes) - radians(".$location['longitude'].")) + sin(radians(".$location['latitude'].")) * sin(radians(latitude))))";
                        $user_ads->whereRaw("{$haversine} < ?", [60]);
                    }
                    $results[] = $user_ads->paginate($per_page_records);
                }
            }
        }
        $return = [];
        foreach ($results as $k => $datas) {
            $setting[$k]['perpage_for'] = $all_tag[$k];
            $setting[$k]['total'] = $datas->total();
            $setting[$k]['perpage'] = $per_page_records;

            $result = array();
            if(!empty($datas)){
                $i=0;
                foreach ($datas as $key => $val) {
                    // dd($val);
                    //\DB::enableQueryLog();
                    $value = DB::table('user_ads')
                        ->select([
                            'users.id as user_id',
                            'users.first_name',
                            'users.last_name',
                            'users.email',
                            'users.avatar_type',
                            'users.avatar_location',
                            'users.slug',
                            'users.updated_at as updated_at',
                            'profile.phone',
                            'profile.experience',
                            'profile.about',
                            'profile.address',
                            'profile.city',
                            'profile.state',
                            'profile.country',
                            'profile.pincode',
                            'profile.latitude',
                            'profile.longitudes',
                            'skill.name as skillname',
                            'skill.avatar as skillavatar',
                            'user_ads.title',
                            'user_ads.description',
                            'user_ads.id as ads_id',
                            'user_ads.price_per_hour',
                            'user_ads.price_per_day',
                            'user_ads.show_price',
                            'user_ads.address AS ads_address',
                            'user_ads.city AS ads_city',
                            'user_ads.state AS ads_state',
                            'user_ads.country AS ads_country',
                            'user_ads.pincode AS ads_pincode',
                            'sp_skill.price_per_hour as sp_skill_price_per_hour',
                            'sp_skill.price_per_day as sp_skill_price_per_day',
                            'sp_skill.show_price as sp_skill_show_price',
                            'currency.symbol as currency_symbol'

                        ])
                        ->leftjoin('users', 'users.id', '=', 'user_ads.user_id')
                        ->leftjoin('profile', 'profile.user_id', '=', 'user_ads.user_id')
                        ->leftjoin('skill', 'skill.id', '=', 'user_ads.skill_id')
                        ->leftjoin('sp_skill', 'sp_skill.skill_id', '=', 'user_ads.skill_id')
                        ->leftjoin('currency', 'currency.id', '=', 'sp_skill.currency_id')
                        ->where('users.id', '=',$val->user_id)
                        ->whereNull('users.deleted_at')
                        ->where('user_ads.id', '=',$val->id)->first();
                     //dd($value);
                            //dd(\DB::getQueryLog());
                    if(!empty($value->slug)){
                        $userAverageRating = Feedback::user_average_rating($value->user_id);
                        $userAverageRating = round($userAverageRating);
                        $user = User::find($value->user_id);
                        $isOnline = $user->isOnline();
                        $last_login = $user->updated_at->diffForHumans();
                        $result[$i]['heading'] = $all_tag[$k];
                        $result[$i]['user_id'] = $value->user_id;
                        $result[$i]['rating'] = $userAverageRating;
                        $result[$i]['isOnline'] = 0;
                        $result[$i]['able_to_send_message'] = 0;

                        if(!empty($loged)){
                            if(!($loged->id == $value->user_id))
                                $result[$i]['able_to_send_message'] = 1;
                        }

                        if($isOnline)
                            $result[$i]['isOnline'] = 1;

                        if($value->avatar_type == "gravatar"){
                            $result[$i]['sp_image']= "/storage/avatars/dummy.png";
                        }else if ($value->avatar_type == "storage"){
                            if($value->avatar_location){
                                $result[$i]['sp_image']="/storage/".$value->avatar_location;
                            } else {
                                $result[$i]['sp_image']= "/storage/avatars/dummy.png";
                            }
                        }else{
                            $social_Account = SocialAccount::where('user_id','=',$value->user_id)->where('provider','=',$value->avatar_type)->first();
                            if(!empty($social_Account))
                                $result[$i]['sp_image']=$social_Account->avatar;
                        }

                        $result[$i]['sp_name']=$value->first_name ." ".$value->last_name;
                        $result[$i]['sp_about']=$value->about;
                        $result[$i]['sp_slug']=$value->slug;
                        $result[$i]['sp_last_login']=$last_login;
                        $result[$i]['email']=$value->email;
                        $result[$i]['address']=$value->address;
                        $result[$i]['city']=$value->city;
                        $result[$i]['state']=$value->state;
                        $result[$i]['country']=$value->country;
                        $result[$i]['latitude']=$value->latitude;
                        $result[$i]['longitudes']=$value->longitudes;
                        $result[$i]['phone']=$value->phone;
                        $result[$i]['experience']=$value->experience;
                        $result[$i]['skillname']=$value->skillname;
                        $result[$i]['skillavatar']="/storage/skills/".$value->skillavatar;

                        $result[$i]['ads_id']=$val->id;
                        $result[$i]['ads_address']=$value->ads_address;
                        $result[$i]['ads_city']=$value->ads_city;
                        $result[$i]['ads_state']=$value->ads_state;
                        $result[$i]['ads_country']=$value->ads_country;
                        $result[$i]['ads_pincode']=$value->ads_pincode;
                        $result[$i]['user_ads_title']=$val->title;
                        // echo "<pre>";print_r($value->latitude);echo "</pre>";exit;
                        //Old code
                        // if($value->sp_skill_show_price == "hour"){
                        //  $result[$i]['final_price']=$value->sp_skill_price_per_hour;
                        // } else {
                        //  $result[$i]['final_price']=$value->sp_skill_price_per_day;
                        // }
                        if( $value->show_price == 'hour' ) {
                            $result[$i]['final_price']= $value->price_per_hour;
                        } elseif( $value->show_price == 'day' ) {
                            $result[$i]['final_price']= $value->price_per_day;
                        } else {
                            $result[$i]['final_price']= $value->price_per_hour .'-'.$value->price_per_day;
                        }

                        $result[$i]['pricetype']=$value->show_price;
                        $result[$i]['currency']=$value->currency_symbol;
                        $result[$i]['user_ads_description']=$val->description;

                        $result[$i]['ads_description_total_chars']=strlen($val->description);

                        $i++;
                    }
                }
            }
                //dd($result);
            $return[] = $result;
        }

        // $getDefaultAdvertise = Advertisements::with('getSkill')->where('isFront', 1)->get();
        if( !empty($request->skillid) || $request->skillid != '') {
            $skillid    = $request->skillid;
            $all_tags    = json_decode($skillid, true);
            $allSkilsArr = [];
            foreach ($all_tags as $key => $skill) {
                // code...
                $allSkilsArr[] = $skill['id'];
            }
            // dd($allSkilsArr);
            $getads = Advertisements::with('getSkill')->where('city', $request->locality)->whereIn('skill_id', $allSkilsArr)->orderBy('id', 'desc')->get();
        } else {
            $getads = Advertisements::with('getSkill')->where('city', $request->locality)->orderBy('id', 'desc')->get();
        }


        if( !empty($return) ){
            $message = array('message' => __('Success'));
            $errors = array();
            $resultdata = array(
                'results' => $return,
                'settings' => $setting,
                // 'defaultSponsorAds' => $getDefaultAdvertise,
                'allSponsorsAds' => $getads
            );
        } else {
            $message = array();
            $errors = array('message' => __('Whoops, looks like something went wrong'));
            $resultdata = array();
        }

        return response()->json(
        [
            'errors' => $errors,
            "status" => 200,
            "message" => $message,
            "data" => $resultdata,
        ],200);
    }

    public function searchTag(Request $request){
        return response()->json($this->getSearchResults($request, 10));
    }

    public function getSearchResults($request, $per_page_records){
        $loged = Auth::user();
        if( !empty($request->skillid) || $request->skillid != '' ) {
            $skillid    = $request->skillid;
            $all_tag    = json_decode($skillid);
        } else {
            $allSkills = Skill::all();
            $newSkillsId = array();
            foreach( $allSkills as $skeys => $skill ) {
                $newSkillsId[] = $skill;
            }
            $skillid = $newSkillsId;
            $all_tag = $skillid;
        }

        $skillname    =$request->skillname;
        $where    =$request->where;
        $street_number    =$request->street_number;
        $route    =$request->route;
        $city    =$request->locality;  //city
        $state    =$request->administrative_area_level_1; //state
        $country    =$request->country; // india
        $postal_code    =$request->postal_code;
        $lng    = $request->lng;
        $lat    = $request->lat;
        $sort_by = $request->sort_by;
        $location['latitude']= $lat;
        $location['longitude']= $lng;
        $ratings = json_decode($request->rating);
        $profile_badge = $request->profile_badge;
        $setting =array();

        $my_results = [];

        if(!empty($all_tag) && $all_tag!="null"){
            //get all tags from array
            $newTags = [];
            foreach ($all_tag as $key => $value) {
                $newTags[]= $value->name;
            }
            // echo "<pre>"; print_r($newTags); echo "</pre>";
            // die("called");
            // foreach ($all_tag as $key => $value) {  //a,b,c,d
            // }
            DB::enableQueryLog();
            $sp_skills_data = DB::table('sp_skill')->select('sp_skill.*');
            $sp_skills_data->where(['status' => 1]);
            $sp_skills_data->where(function ($main_query) use($newTags){
                /*$main_query->whereIn('skill_id', function($query) use($value){
                    $query->select('id')->from('skill')->where('name', $value->name);
                });*/

                $main_query->orWhereIn('sp_skill.skill_id', function ($query) use ($newTags) {
                    $query->select('id')->from('skill')->whereIn('name', $newTags);
                });

                // $main_query->orWhereIn('tags', $newTags);
                // $main_query->orWhere('tags', 'LIKE','%'.$value->name.'%');
            });
            $sp_skills_data->whereIn('sp_skill.user_id', function($query){
                $query->select('id')->from('users')->where('is_sp', 1)->where('active', 1);
            });

            if(!empty($city) && $city != "null"){
                $sp_skills_data->where('sp_skill.city', 'LIKE','%'.$city.'%');
            }

            // if(!empty($state) && $state!="null"){
            //     $sp_skills_data->where('state', 'LIKE','%'.$state.'%');
            // }

            if(!empty($country) && $country!="null"){
                $sp_skills_data->where('sp_skill.country', 'LIKE','%'.$country.'%');
            }

            if($sp_skills_data->paginate()->count() == 0 &&!empty($city) && $city != "null"){
                $sp_new = DB::table('sp_skill')->select('sp_skill.*');
                $sp_new->where(['status' => 1]);

                 if( !empty($ratings) && $ratings!="null") {
                    $sp_new->leftJoin('overall_profile_ratings', 'overall_profile_ratings.uid', '=', 'sp_skill.user_id');
                    $sp_new->whereIn('overall_profile_ratings.total_rating', $ratings);
                 }

                if( !empty( $profile_badge ) && $profile_badge ) {
                    $sp_new->leftJoin('profile', 'profile.user_id', '=', 'sp_skill.user_id');
                    $sp_new->where('profile.badge_id', '=', $profile_badge);
                }

                $sp_new->where(function ($main_query) use($newTags){
                    // $main_query->whereIn('skill_id', function($query) use($value){
                    //     $query->select('id')->from('skill')->where('name', $value->name);
                    // });
                    // $main_query->orWhere('tags', 'LIKE','%'.$value->name.'%');
                    $main_query->orWhereIn('sp_skill.skill_id', function ($query) use ($newTags) {
                        $query->select('id')->from('skill')->whereIn('name', $newTags);
                    });
                });
                $sp_new->whereIn('sp_skill.user_id', function($query){
                    $query->select('id')->from('users')->where('is_sp', 1)->where('active', 1);
                });
                $haversine = "(6371 * acos(cos(radians(".$location['latitude'].")) * cos(radians(latitude)) * cos(radians(longitudes) - radians(".$location['longitude'].")) + sin(radians(".$location['latitude'].")) * sin(radians(latitude))))";
                $sp_new->whereRaw("{$haversine} < ?", [60]);
                // $sp_new->whereRaw("{$haversine} < ?", [0]);
                try {

                    $my_results[] = $sp_new->orderBy('sp_skill.id', 'desc')->get();
                } catch (\Exception $e) {
                    // If there's an error, you can log or handle it accordingly
                    // For debugging purposes, you can check the error message like this:
                    echo $e->getMessage();
                }
            } else {
                if( !empty($ratings) && $ratings!="null") {

                    $sp_skills_data->leftJoin('overall_profile_ratings', 'overall_profile_ratings.uid', '=', 'sp_skill.user_id');
                    $sp_skills_data->whereIn('overall_profile_ratings.total_rating', $ratings);
                }

                if( !empty( $profile_badge ) && $profile_badge!="null" ) {
                    $sp_skills_data->leftjoin('profile', 'profile.user_id', '=', 'sp_skill.user_id');
                    $sp_skills_data->where('profile.badge_id', '=', $profile_badge);
                }
                try {
                    // Get the results using get() or paginate() method
                    $my_results[] = $sp_skills_data->orderBy('sp_skill.id', 'desc')->get();
                } catch (\Exception $e) {
                    // If there's an error, you can log or handle it accordingly
                    // For debugging purposes, you can check the error message like this:
                    echo $e->getMessage();
                }
            }
        }
        //
        $return = [];
        // dd($my_results);
        $processedUsers = [];


        foreach ($my_results as $k => $datas) {
            $setting[$k]['total'] = count($datas);
            // $setting[$k]['perpage'] = $per_page_records;
            $result = array();
            if(!empty($datas)){
                $i=0;
                $existingUserId = '';
                foreach ($datas as $key => $value1) {
                    $userId = $value1->user_id;
                    $spSkillId = $value1->id;

                    if (in_array($userId, $processedUsers)) {
                        continue; // Skip processing this user as it's already been processed
                    }

                    $processedUsers[] = $userId;
                    DB::enableQueryLog();
                    $sp_skill_value = DB::table('sp_skill');
                    $sp_skill_value->select([
                        'users.id as user_id',
                        'users.first_name',
                        'users.last_name',
                        'users.email',
                        'users.avatar_type',
                        'users.avatar_location',
                        'users.slug',
                        'users.updated_at as updated_at',
                        'profile.badge_id',
                        'profile.phone',
                        'profile.experience',
                        'profile.about',
                        'profile.address',
                        'profile.city',
                        'profile.state',
                        'profile.country',
                        'profile.pincode',
                        'profile.latitude',
                        'profile.longitudes',
                        'skill.name as skillname',
                        'skill.avatar as skillavatar',
                        'currency.symbol as currency',
                        'sp_skill.id AS sID',
                        'sp_skill.tags',
                        'sp_skill.description as sp_skill_description',
                        'sp_skill.price_per_hour as sp_skill_price_per_hour',
                        'sp_skill.price_per_day as sp_skill_price_per_day',
                        'sp_skill.show_price as sp_skill_show_price',
                        'sp_skill.offer_discount as sp_skill_offer_discount',
                        'sp_skill.offer_desc as sp_skill_offer_desc',
                        'sp_skill.offer_img as sp_skill_offer_img',
                        'sp_skill.offer_start_date as sp_skill_offer_start_date',
                        'sp_skill.offer_end_date as sp_skill_offer_end_date',
                        'sp_skill.address AS sp_skill_address',
                        'sp_skill.city AS sp_skill_city',
                        'sp_skill.state AS sp_skill_state',
                        'sp_skill.country AS sp_skill_country',
                        'sp_skill.pincode AS sp_skill_pincode',
                        'sp_skill.latitude AS sp_skill_latitude',
                        'sp_skill.longitudes AS sp_skill_longitudes',
                        'user_badges.id AS bid',
                        'user_badges.badge_name',
                        'user_badges.image AS badge_image',
                    ]);
                    $sp_skill_value->selectRaw('CASE
                        WHEN MONTH(profile_visitors.created_at) = MONTH(CURDATE()) THEN "Most interesting"
                        ELSE ""
                        END AS interesting_month_status')
                    ->leftjoin('users', 'users.id', '=', 'sp_skill.user_id')
                    ->leftjoin('profile', 'profile.user_id', '=', 'sp_skill.user_id')
                    ->leftjoin('user_badges', 'user_badges.id', '=', 'profile.badge_id')
                    ->leftjoin('skill', 'skill.id', '=', 'sp_skill.skill_id')
                    ->leftjoin('currency', 'currency.id', '=', 'sp_skill.currency_id')
                    ->leftJoin('profile_visitors', 'users.id', '=', 'profile_visitors.visited_user_id')
                    ->leftJoin(DB::raw('(SELECT visited_user_id, COUNT(*) as visitor_count FROM profile_visitors GROUP BY visited_user_id) as visitors'), 'users.id', '=', 'visitors.visited_user_id')
                    ->where('users.id', '=',$userId)
                    ->where('sp_skill.id', '=',$spSkillId);
                    $value = $sp_skill_value->first();
                    // echo "<pre>"; print_r($value); echo "</pre>";
                    $userAverageRating = Feedback::user_average_rating($value->user_id);
                    $userAverageRating = round($userAverageRating);
                    $user = User::find($value->user_id);
                    // $last_login = $user->updated_at->diffForHumans();
                    $result[$i]['heading'] = $all_tag[$k];
                    $result[$i]['user_id'] = $value->user_id;
                    $result[$i]['rating'] = $userAverageRating;
                    $result[$i]['badge_id'] = $value->badge_id;
                    $result[$i]['badge_name'] = $value->badge_name;
                    $result[$i]['badge_image'] = $value->badge_image;
                    $result[$i]['isOnline'] = 0;
                    $result[$i]['able_to_send_message'] = 0;
                    $result[$i]['sp_name']=$value->first_name ." ".$value->last_name;
                    $result[$i]['sp_about']=$value->about;
                    $result[$i]['sp_slug']=$value->slug;
                    $result[$i]['isInterested'] = $value->interesting_month_status;
                    // $result[$i]['sp_last_login']=$last_login;
                    $result[$i]['currency']=$value->currency;
                    $result[$i]['email']=$value->email;
                    $result[$i]['address']=$value->sp_skill_address ? $value->sp_skill_address : $value->address;
                    $result[$i]['city']=$value->sp_skill_city ? $value->sp_skill_city : $value->city;
                    $result[$i]['state']=$value->sp_skill_state ? $value->sp_skill_state : $value->state;
                    $result[$i]['country']=$value->sp_skill_country ? $value->sp_skill_country : $value->country;
                    $result[$i]['latitude']=$value->sp_skill_latitude ? $value->sp_skill_latitude : $value->latitude;
                    $result[$i]['longitudes']=$value->sp_skill_longitudes ? $value->sp_skill_longitudes : $value->longitudes;
                    $result[$i]['phone']=$value->phone;
                    $result[$i]['experience']=$value->experience;

                    if(!empty($loged)){
                        if(!($loged->id == $value->user_id))
                            $result[$i]['able_to_send_message'] = 1;
                    }

                    // if($isOnline)
                    //     $result[$i]['isOnline'] = 1;

                    if($value->avatar_type == "gravatar"){
                        $result[$i]['sp_image']= "/storage/avatars/dummy.png";
                    }else if ($value->avatar_type == "storage"){
                        if($value->avatar_location){
                            $result[$i]['sp_image']="/storage/".$value->avatar_location;
                        } else {
                            $result[$i]['sp_image']= "/storage/avatars/dummy.png";
                        }
                    }else{
                        $social_Account = SocialAccount::where('user_id','=',$value->user_id)->where('provider','=',$value->avatar_type)->first();
                        if(!empty($social_Account))
                            $result[$i]['sp_image']=$social_Account->avatar;
                    }

                    $Spskills = Spskill::where('user_id',$value->user_id)->where('status',1)->get();
                    if(!empty($Spskills)){
                        $j=0;
                        foreach ($Spskills as $key => $Spskill) {
                            $result[$i]['sp_skills_data'][] = [
                                'sp_skills_id' => $Spskill->id,
                                'sp_skill_images' => "/storage/skills/".$Spskill->skill->avatar
                            ];
                            $result[$i]['sp_skill_images'][$j++]="/storage/skills/".$Spskill->skill->avatar;
                            $result[$i]['sp_user_all_skills'][$key]['ID'] = $Spskill->id;
                            $result[$i]['sp_user_all_skills'][$key]['SID'] = $Spskill->skill_id;
                            $result[$i]['sp_user_all_skills'][$key]['s_name'] = $Spskill->skill->name;
                            $result[$i]['sp_user_all_skills'][$key]['s_avatar'] = "/storage/skills/".$Spskill->skill->avatar;
                            $result[$i]['sp_user_all_skills'][$key]['s_description'] = $Spskill->description;
                            $result[$i]['sp_user_all_skills'][$key]['s_price_type'] = $Spskill->show_price;
                            $result[$i]['sp_user_all_skills'][$key]['s_price_per_hour'] = $Spskill->price_per_hour;
                            $result[$i]['sp_user_all_skills'][$key]['s_price_per_day'] = $Spskill->price_per_day;
                            $result[$i]['sp_user_all_skills'][$key]['s_offer_discount'] = $Spskill->offer_discount;
                            $result[$i]['sp_user_all_skills'][$key]['s_offer_desc'] = $Spskill->offer_desc;
                            $result[$i]['sp_user_all_skills'][$key]['s_offer_img'] = $Spskill->offer_img ? "/storage/spskills/".$Spskill->offer_img : $Spskill->offer_img;
                            $result[$i]['sp_user_all_skills'][$key]['s_offer_start_date'] = $Spskill->offer_start_date;
                            $result[$i]['sp_user_all_skills'][$key]['s_offer_end_date'] = $Spskill->offer_end_date;
                            $result[$i]['sp_user_all_skills'][$key]['s_address'] = $Spskill->address;
                            $result[$i]['sp_user_all_skills'][$key]['s_city'] = $Spskill->city;
                            $result[$i]['sp_user_all_skills'][$key]['s_state'] = $Spskill->state;
                            $result[$i]['sp_user_all_skills'][$key]['s_country'] = $Spskill->country;
                            $result[$i]['sp_user_all_skills'][$key]['s_latitude'] = $Spskill->latitude;
                            $result[$i]['sp_user_all_skills'][$key]['s_longitudes'] = $Spskill->longitudes;
                            $now = date("Y-m-d");
                            $now = strtotime($now);
                            $offer_start = strtotime($Spskill->offer_start_date);
                            $offer_end = strtotime($Spskill->offer_end_date);
                            if($Spskill->show_price =="hour"){
                                $s_final_price = (float)$Spskill->price_per_hour;
                            }else{
                                $s_final_price = (float)$Spskill->price_per_day;
                            }
                            $result[$i]['sp_user_all_skills'][$key]['s_normal_price'] = $s_final_price;
                            if($offer_start  <= $now  &&  $now  <= $offer_end){
                                $s_final_price = (float)($s_final_price - (($s_final_price * $Spskill->offer_discount)/100 ) );
                                $result[$i]['sp_user_all_skills'][$key]['s_si_offer'] = 1;
                            } else {
                                $result[$i]['sp_user_all_skills'][$key]['s_si_offer'] = 0;
                            }
                            $result[$i]['sp_user_all_skills'][$key]['s_final_price'] = $s_final_price;
                        }
                    }

                    $result[$i]['skillID']=$value->sID;
                    $result[$i]['skillname']=$value->skillname;
                    $result[$i]['skillavatar']="/storage/skills/".$value->skillavatar;

                    // $result[$i]['sp_skill_description']=$value->sp_skill_description;
                    // $result[$i]['sp_skill_price_per_hour']=$value->sp_skill_price_per_hour;
                    // $result[$i]['sp_skill_price_per_day']=$value->sp_skill_price_per_day;
                    // $result[$i]['sp_skill_show_price']=__("strings.new.".$value->sp_skill_show_price);
                    // $result[$i]['sp_skill_offer_discount']=$value->sp_skill_offer_discount;
                    // $result[$i]['sp_skill_offer_desc']=$value->sp_skill_offer_desc;
                    // $result[$i]['sp_skill_offer_img']="/storage/spskills/".$value->sp_skill_offer_img;
                    // $result[$i]['sp_skill_offer_start_date']=$value->sp_skill_offer_start_date;
                    // $result[$i]['sp_skill_offer_end_date']=$value->sp_skill_offer_end_date;

                    $result[$i]['sp_skill_description']=$value1->description;
                    $result[$i]['sp_skill_price_per_hour']=$value1->price_per_hour;
                    $result[$i]['sp_skill_price_per_day']=$value1->price_per_day;
                    $result[$i]['sp_skill_show_price']=__("strings.new.".$value1->show_price);
                    $result[$i]['sp_skill_offer_discount']=$value1->offer_discount;
                    $result[$i]['sp_skill_offer_desc']=$value1->offer_desc;
                    $result[$i]['sp_skill_offer_img']="/storage/spskills/".$value1->offer_img;
                    $result[$i]['sp_skill_offer_start_date']=$value1->offer_start_date;
                    $result[$i]['sp_skill_offer_end_date']=$value1->offer_end_date;

                    $result[$i]['sp_skill_si_offer']=0;

                    // price calculation
                    $now = date("Y-m-d");
                    $now = strtotime($now);
                    $offer_start = strtotime($value1->offer_start_date);
                    $offer_end = strtotime($value1->offer_end_date);
                    if($value1->show_price =="hour"){
                        $final_price = (float)$value1->price_per_hour;
                    }else{
                        $final_price = (float)$value1->price_per_day;
                    }
                    $result[$i]['normal_price']=$final_price;
                    if($offer_start  <= $now  &&  $now  <= $offer_end){
                        $final_price = (float)($final_price - (($final_price *$value1->offer_discount)/100 ) );
                        $result[$i]['sp_skill_si_offer']=1;
                    }
                    $result[$i]['final_price']=$final_price;
                    $i++;
                }
            }
            $price = array();
            $rating_val = array();
            foreach ($result as $key => $row){
                $price[$key] = $row['final_price'];
                $rating_val[$key] = $row['rating'];
            }

            // array_multisort($price, SORT_ASC, $result);
            // if($sort_by == 'desc_price'){
            //     array_multisort($price, SORT_DESC, $result);
            // }else if($sort_by == 'desc_rating'){
            //     array_multisort($rating_val, SORT_DESC, $result);
            // }else if($sort_by == 'asc_rating'){
            //     array_multisort($rating_val, SORT_ASC, $result);
            // }
            // echo "<pre>"; print_r($result); echo "</pre>";
            $return[] = $result;
        }

        // $getDefaultAdvertise = Advertisements::with('getSkill')->where('isFront', 1)->get();
        // $getads = Advertisements::with('getSkill')->orderBy('id', 'desc')->get();

        if( !empty($request->skillid) || $request->skillid != '') {
            $skillid    = $request->skillid;
            $all_tags    = json_decode($skillid, true);
            $allSkilsArr = [];
            foreach ($all_tags as $key => $skill) {
                // code...
                $allSkilsArr[] = $skill['id'];
            }
            // dd($allSkilsArr);
            $getads = Advertisements::with('getSkill')->where('city', $request->locality)->whereIn('skill_id', $allSkilsArr)->orderBy('id', 'desc')->get();
        } else {
            $getads = Advertisements::with('getSkill')->where('city', $request->locality)->orderBy('id', 'desc')->get();
        }

        if( !empty($return) ){
            $message = array('message' => __('Success'));
            $errors = array();
            $resultdata = array(
                'results' => $return,
                'settings' => $setting,
                // 'defaultSponsorAds' => $getDefaultAdvertise,
                'allSponsorsAds' => $getads
            );
        } else {
            $message = array();
            $errors = array('message' => __('Whoops, looks like something went wrong'));
            $resultdata = array();
        }

        return response()->json(
        [
            'errors' => $errors,
            "status" => 200,
            "message" => $message,
            "data" => $resultdata,
        ],200);
    }

    // Old MEthod of Profile Search
    public function getSearchResults1($request, $per_page_records){
        DB::enableQueryLog();

        $loged = Auth::user();
        if( !empty($request->skillid) || $request->skillid != '' ) {
            $skillid    = $request->skillid;
            $all_tag    = json_decode($skillid);
        } else {
            $allSkills = Skill::all();
            $newSkillsId = array();
            foreach( $allSkills as $skeys => $skill ) {
                $newSkillsId[] = $skill;
            }
            $skillid = $newSkillsId;
            $all_tag = $skillid;
        }


        $skillname    =$request->skillname;
        $where    =$request->where;
        $street_number    =$request->street_number;
        $route    =$request->route;
        $city    =$request->locality;  //city
        $state    =$request->administrative_area_level_1; //state
        $country    =$request->country; // india
        $postal_code    =$request->postal_code;
        $lng    =$request->lng;
        $lat    =$request->lat;
        $sort_by = $request->sort_by;
        $location['latitude']=$lat;
        $location['longitude']=$lng;
        $setting =array();
        // $all_tag = json_decode($skillid);
        $my_results = [];
        if(!empty($all_tag) && $all_tag!="null"){
            foreach ($all_tag as $key => $value) {


                $sp_skills_data = DB::table('sp_skill');
                $sp_skills_data->where(['status' => 1]);
                $sp_skills_data->where(function ($main_query) use($value){
                    $main_query->whereIn('skill_id', function($query) use($value){
                        $query->select('id')->from('skill')->where('name', $value->name);
                    });
                    $main_query->orWhere('tags', 'LIKE','%'.$value->name.'%');
                });
                $sp_skills_data->whereIn('user_id', function($query){
                    $query->select('id')->from('users')->where('is_sp', 1)->where('active', 1);
                });

                if(!empty($city) && $city != "null"){
                    $sp_skills_data->where('city', 'LIKE','%'.$city.'%');
                }

                if(!empty($state) && $state!="null"){
                    $sp_skills_data->where('state', 'LIKE','%'.$state.'%');
                }

                if(!empty($country) && $country!="null"){
                    $sp_skills_data->where('country', 'LIKE','%'.$country.'%');
                }

                if($sp_skills_data->paginate()->count() == 0 && !empty($city) && $city != "null"){

                    $sp_new = DB::table('sp_skill');
                    $sp_new->where(['status' => 1]);
                    $sp_new->where(function ($main_query) use($value){
                        $main_query->whereIn('skill_id', function($query) use($value){
                            $query->select('id')->from('skill')->where('name', $value->name);
                        });
                        $main_query->orWhere('tags', 'LIKE','%'.$value->name.'%');
                    });
                    $sp_new->whereIn('user_id', function($query){
                        $query->select('id')->from('users')->where('is_sp', 1)->where('active', 1);
                    });

                    $haversine = "(6371 * acos(cos(radians(".$location['latitude'].")) * cos(radians(latitude)) * cos(radians(longitudes) - radians(".$location['longitude'].")) + sin(radians(".$location['latitude'].")) * sin(radians(latitude))))";
                    // echo "<pre>";print_r($haversine);echo "</pre>";exit;
                    $sp_new->whereRaw("{$haversine} < ?", [60]);
                    // echo $sp_new->toSql(); echo "\n\n";
                    $my_results[] = $sp_new->paginate($per_page_records);

                } else {
                    // echo $sp_skills_data->toSql(); echo "\n\n";
                    $haversine = "(6371 * acos(cos(radians(".$location['latitude'].")) * cos(radians(latitude)) * cos(radians(longitudes) - radians(".$location['longitude'].")) + sin(radians(".$location['latitude'].")) * sin(radians(latitude))))";
                    $sp_skills_data->whereRaw("{$haversine} < ?", [60]);

                    $my_results[] = $sp_skills_data->paginate($per_page_records);
                }
                // $my_results[] = $sp_skills_data->paginate($per_page_records);
            }
        }

        $return = [];
        try{

            foreach ($my_results as $k => $datas) {

                $setting[$k]['total'] = $datas->total();
                $setting[$k]['perpage'] = $per_page_records;

                $result = array();
                if(!empty($datas)){

                    $i=0;
                    foreach ($datas as $key => $value1) {

                        DB::enableQueryLog();
                        $value = DB::table('sp_skill')
                                ->select([
                                    'users.id as user_id',
                                    'users.first_name',
                                    'users.last_name',
                                    'users.email',
                                    'users.avatar_type',
                                    'users.avatar_location',
                                    'users.slug',
                                    'users.updated_at as updated_at',
                                    'profile.phone',
                                    'profile.experience',
                                    'profile.about',
                                    'profile.address',
                                    'profile.city',
                                    'profile.state',
                                    'profile.country',
                                    'profile.pincode',
                                    'profile.latitude',
                                    'profile.longitudes',
                                    'skill.name as skillname',
                                    'skill.avatar as skillavatar',
                                    'currency.symbol as currency',
                                    'sp_skill.tags', /// change by bindiya
                                    'sp_skill.description as sp_skill_description',
                                    'sp_skill.price_per_hour as sp_skill_price_per_hour',
                                    'sp_skill.price_per_day as sp_skill_price_per_day',
                                    'sp_skill.show_price as sp_skill_show_price',
                                    'sp_skill.offer_discount as sp_skill_offer_discount',
                                    'sp_skill.offer_desc as sp_skill_offer_desc',
                                    'sp_skill.offer_img as sp_skill_offer_img',
                                    'sp_skill.offer_start_date as sp_skill_offer_start_date',
                                    'sp_skill.offer_end_date as sp_skill_offer_end_date',
                                    'sp_skill.address AS sp_skill_address',
                                    'sp_skill.city AS sp_skill_city',
                                    'sp_skill.state AS sp_skill_state',
                                    'sp_skill.country AS sp_skill_country',
                                    'sp_skill.pincode AS sp_skill_pincode',
                                    'sp_skill.latitude AS sp_skill_latitude',
                                    'sp_skill.longitudes AS sp_skill_longitudes',
                                ])
                                ->leftjoin('users', 'users.id', '=', 'sp_skill.user_id')
                                ->leftjoin('profile', 'profile.user_id', '=', 'sp_skill.user_id')
                                ->leftjoin('skill', 'skill.id', '=', 'sp_skill.skill_id')
                                ->leftjoin('currency', 'currency.id', '=', 'sp_skill.currency_id')
                                ->where('users.id', '=',$value1->user_id)
                                ->whereNull('users.deleted_at')
                                ->where('sp_skill.id', '=',$value1->id)->first();

                        if(!empty($value->slug)){
                            $userAverageRating = Feedback::user_average_rating($value->user_id);
                            $userAverageRating = round($userAverageRating);
                            $user = User::find($value->user_id);
                            $isOnline = $user->isOnline();
                            $last_login = $user->updated_at->diffForHumans();
                            $result[$i]['heading'] = $all_tag[$k];
                            $result[$i]['user_id'] = $value->user_id;
                            $result[$i]['rating'] = $userAverageRating;
                            $result[$i]['isOnline'] = 0;
                            $result[$i]['able_to_send_message'] = 0;

                            if(!empty($loged)){
                                if(!($loged->id == $value->user_id))
                                    $result[$i]['able_to_send_message'] = 1;
                            }

                            if($isOnline)
                                $result[$i]['isOnline'] = 1;

                            if($value->avatar_type == "gravatar"){
                                $result[$i]['sp_image']= "/storage/avatars/dummy.png";
                            }else if ($value->avatar_type == "storage"){
                                if($value->avatar_location){
                                    $result[$i]['sp_image']="/storage/".$value->avatar_location;
                                } else {
                                    $result[$i]['sp_image']= "/storage/avatars/dummy.png";
                                }
                            }else{
                                $social_Account = SocialAccount::where('user_id','=',$value->user_id)->where('provider','=',$value->avatar_type)->first();
                                if(!empty($social_Account))
                                    $result[$i]['sp_image']=$social_Account->avatar;
                            }

                            $Spskills = Spskill::where('user_id',$value->user_id)->where('status',1)->get();
                            if(!empty($Spskills)){
                                $j=0;
                                foreach ($Spskills as $key => $Spskill) {
                                    $result[$i]['sp_skill_images'][$j++]="/storage/skills/".$Spskill->skill->avatar;
                                }
                            }

                            $result[$i]['sp_name']=$value->first_name ." ".$value->last_name;
                            $result[$i]['sp_about']=$value->about;
                            $result[$i]['sp_slug']=!empty($value->slug) ? $value->slug : '';
                            $result[$i]['sp_last_login']=$last_login;
                            $result[$i]['currency']=$value->currency;
                            $result[$i]['email']=$value->email;
                            $result[$i]['address']=$value->sp_skill_address ? $value->sp_skill_address : $value->address;
                            $result[$i]['city']=$value->sp_skill_city ? $value->sp_skill_city : $value->city;
                            $result[$i]['state']=$value->sp_skill_state ? $value->sp_skill_state : $value->state;
                            $result[$i]['country']=$value->sp_skill_country ? $value->sp_skill_country : $value->country;
                            $result[$i]['latitude']=$value->sp_skill_latitude ? $value->sp_skill_latitude : $value->latitude;
                            $result[$i]['longitudes']=$value->sp_skill_longitudes ? $value->sp_skill_longitudes : $value->longitudes;
                            $result[$i]['phone']=$value->phone;
                            $result[$i]['experience']=$value->experience;
                            $result[$i]['skillname']=$value->skillname;
                            $result[$i]['skillavatar']="/storage/skills/".$value->skillavatar;

                            // $result[$i]['sp_skill_description']=$value->sp_skill_description;
                            // $result[$i]['sp_skill_price_per_hour']=$value->sp_skill_price_per_hour;
                            // $result[$i]['sp_skill_price_per_day']=$value->sp_skill_price_per_day;
                            // $result[$i]['sp_skill_show_price']=__("strings.new.".$value->sp_skill_show_price);
                            // $result[$i]['sp_skill_offer_discount']=$value->sp_skill_offer_discount;
                            // $result[$i]['sp_skill_offer_desc']=$value->sp_skill_offer_desc;
                            // $result[$i]['sp_skill_offer_img']="/storage/spskills/".$value->sp_skill_offer_img;
                            // $result[$i]['sp_skill_offer_start_date']=$value->sp_skill_offer_start_date;
                            // $result[$i]['sp_skill_offer_end_date']=$value->sp_skill_offer_end_date;

                            $result[$i]['sp_skill_description']=$value1->description;
                            $result[$i]['sp_skill_price_per_hour']=$value1->price_per_hour;
                            $result[$i]['sp_skill_price_per_day']=$value1->price_per_day;
                            $result[$i]['sp_skill_show_price']=__("strings.new.".$value1->show_price);
                            $result[$i]['sp_skill_offer_discount']=$value1->offer_discount;
                            $result[$i]['sp_skill_offer_desc']=$value1->offer_desc;
                            $result[$i]['sp_skill_offer_img']="/storage/spskills/".$value1->offer_img;
                            $result[$i]['sp_skill_offer_start_date']=$value1->offer_start_date;
                            $result[$i]['sp_skill_offer_end_date']=$value1->offer_end_date;

                            $result[$i]['sp_skill_si_offer']=0;

                            // price calculation
                            $now = date("Y-m-d");
                            $now = strtotime($now);
                            $offer_start = strtotime($value1->offer_start_date);
                            $offer_end = strtotime($value1->offer_end_date);
                            if($value1->show_price =="hour"){
                                $final_price = (float)$value1->price_per_hour;
                            }else{
                                $final_price = (float)$value1->price_per_day;
                            }
                            $result[$i]['normal_price']=$final_price;
                            if($offer_start  <= $now  &&  $now  <= $offer_end){
                                $final_price = (float)($final_price - (($final_price *$value1->offer_discount)/100 ) );
                                $result[$i]['sp_skill_si_offer']=1;
                            }
                            $result[$i]['final_price']=$final_price;
                            $i++;
                        }
                    }
                }
                $price = array();
                $rating_val = array();
                foreach ($result as $key => $row){
                    $price[$key] = $row['final_price'];
                    $rating_val[$key] = $row['rating'];
                }
                array_multisort($price, SORT_ASC, $result);
                if($sort_by == 'desc_price'){
                    array_multisort($price, SORT_DESC, $result);
                }else if($sort_by == 'desc_rating'){
                    array_multisort($rating_val, SORT_DESC, $result);
                }else if($sort_by == 'asc_rating'){
                    array_multisort($rating_val, SORT_ASC, $result);
                }

                $return[] = $result;
            }
        } catch(Exception $e) {
            return response()->json([ 'error' => $e->getMessage() ]);
        }

        if( !empty($return) ){
            $message = array('message' => __('Success'));
            $errors = array();
            $resultdata = array('results' => $return, 'settings' => $setting);
        } else {
            $message = array();
            $errors = array('message' => __('Whoops, looks like something went wrong'));
            $resultdata = array();
        }

        return response()->json(
        [
            'errors' => $errors,
            "status" => 200,
            "message" => $message,
            "data" => $resultdata,
        ],200);
    }

    /**
     * Get all skills for append in mobile app .
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getSkills(Request $request)
    {
        $Skills = Skill::where('status',1)->orderBy('name', 'asc')->latest()->get();
        return response()->json(
        [
            'errors' => '',
            "status" => 200,
            "message" => "Success",
            "skills" => $Skills,
        ],200);
    }

    /**
     * Get recent ads for mobile app.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function recentAdsApp(Request $request)
    {
        DB::enableQueryLog();
        try {

            $recent_ads = DB::table('user_ads')->select([
                'users.id as user_id',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.avatar_type',
                'users.avatar_location',
                'users.slug',
                'users.updated_at as updated_at',
                'profile.phone',
                'profile.experience',
                'profile.about',
                'profile.address',
                'profile.city',
                'profile.state',
                'profile.country',
                'profile.pincode',
                'profile.latitude',
                'profile.longitudes',
                'skill.name as skillname',
                'skill.avatar as skillavatar',
                'user_ads.title',
                'user_ads.description',
                'user_ads.id as ads_id',
                'user_ads.price_per_day',
                'user_ads.price_per_hour',
                'user_ads.show_price',
                'user_ads.address as ads_address',
                'currency.id as currency_id',
                'currency.symbol as currency_symbol'
            ])
            // ->withTrashed()
            ->leftjoin('users', 'users.id', '=', 'user_ads.user_id')
            ->leftjoin('profile', 'profile.user_id', '=', 'user_ads.user_id')
            ->leftjoin('skill', 'skill.id', '=', 'user_ads.skill_id')
            ->leftjoin('currency', 'currency.id', '=', 'user_ads.currency_id')
            // ->limit(2)
            ->whereNull('users.deleted_at')
            // ->orderBy('user_ads.id', 'DESC')
            ->inRandomOrder()
            ->take(10)
            ->get();
            // dd('Response OK');
        } catch (Exception $e) {
            return response()->json(
                    [
                        'errors' => $e->getMessage(),
                    ],200);
        }

        $getDefaultAdvertise = Advertisements::with('getSkill')->where('show_in_front_ads', 1)->get();
        // $getads = Advertisements::with('getSkill')->orderBy('id', 'desc')->get();

        if( !empty( $recent_ads ) ) {
            $message =  __('Success!');
            $errors = '';
            $recentAdsData = $recent_ads;
        } else {
            $message = '';
            $errors = __('No recent ads found');
            $recentAdsData = array();
        }

        return response()->json(
        [
            "status" => 200,
            'errors' => $errors,
            "message" => $message,
            'results' => $recentAdsData,
            'defaultSponsorads' => $getDefaultAdvertise,
            // 'allSponsorads' => $getads
        ],200);
    }

    /**
     * Get recent ads for mobile app older.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function recentProfilersAppOlder(Request $request)
    {
        // try {

        $profilers = DB::table('sp_skill')
        ->select([
            'users.id as user_id',
            'users.first_name',
            'users.last_name',
            'users.email',
            'users.avatar_type',
            'users.avatar_location',
            'users.slug',
            'users.updated_at as updated_at',
            'profile.phone',
            'profile.experience',
            'profile.about',
            'profile.address',
            'profile.city',
            'profile.state',
            'profile.country',
            'profile.pincode',
            'profile.latitude',
            'profile.longitudes',
            'skill.name as skillname',
            'skill.avatar as skillavatar',
            'currency.symbol as currency',
            'sp_skill.id AS sID',
            'sp_skill.tags',
            'sp_skill.description as sp_skill_description',
            'sp_skill.price_per_hour as sp_skill_price_per_hour',
            'sp_skill.price_per_day as sp_skill_price_per_day',
            'sp_skill.show_price as sp_skill_show_price',
            'sp_skill.offer_discount as sp_skill_offer_discount',
            'sp_skill.offer_desc as sp_skill_offer_desc',
            'sp_skill.offer_img as sp_skill_offer_img',
            'sp_skill.offer_start_date as sp_skill_offer_start_date',
            'sp_skill.offer_end_date as sp_skill_offer_end_date',
            'sp_skill.address AS sp_skill_address',
            'sp_skill.city AS sp_skill_city',
            'sp_skill.state AS sp_skill_state',
            'sp_skill.country AS sp_skill_country',
            'sp_skill.pincode AS sp_skill_pincode',
            'sp_skill.latitude AS sp_skill_latitude',
            'sp_skill.longitudes AS sp_skill_longitudes',
        ])
        ->selectRaw('CASE
        WHEN MONTH(profile_visitors.created_at) = MONTH(CURDATE()) THEN "Most interesting"
        ELSE ""
        END AS interesting_month_status')
        ->leftjoin('users', 'users.id', '=', 'sp_skill.user_id')
        ->leftjoin('profile', 'profile.user_id', '=', 'sp_skill.user_id')
        ->leftjoin('skill', 'skill.id', '=', 'sp_skill.skill_id')
        ->leftjoin('currency', 'currency.id', '=', 'sp_skill.currency_id')
        ->leftJoin('profile_visitors', 'users.id', '=', 'profile_visitors.visited_user_id')
        ->leftJoin(DB::raw('(SELECT visited_user_id, COUNT(*) as visitor_count FROM profile_visitors GROUP BY visited_user_id) as visitors'), 'users.id', '=', 'visitors.visited_user_id')
        ->where('sp_skill.status', 1)
        ->whereNull('users.deleted_at')
        // ->orderBy('sp_skill.id', 'DESC')
        ->inRandomOrder()
        ->take(10)
        ->get();
        // }catch(Exception $e) {
        //     return response()->json(
        //     [
        //         'errors' => $e->getMessage(),
        //     ],200);
        // }

        $recent_profilers = [];
        foreach($profilers as $i=>$profile) {
            $result = array();

            $userAverageRating = Feedback::user_average_rating($profile->user_id);
            $userAverageRating = round($userAverageRating);
            $user = User::find($profile->user_id);
            $isOnline = $user->isOnline();
            $last_login = $user->updated_at->diffForHumans();
            $result['user_id'] = $profile->user_id;
            $result['rating'] = $userAverageRating;
            $result['isInterested'] = $profile->interesting_month_status;
            $result['able_to_send_message'] = 0;

            if(!empty($loged)){
                if(!($loged->id == $profile->user_id))
                    $result[$i]['able_to_send_message'] = 1;
            }

            if($profile->avatar_type == "gravatar"){
                $result['sp_image']= "/storage/avatars/dummy.png";
            }else if ($profile->avatar_type == "storage"){
                if($profile->avatar_location){
                    $result['sp_image']="/storage/".$profile->avatar_location;
                } else {
                    $result['sp_image']= "/storage/avatars/dummy.png";
                }
            }else{
                $social_Account = SocialAccount::where('user_id','=',$profile->user_id)->where('provider','=',$profile->avatar_type)->first();
                if(!empty($social_Account))
                    $result['sp_image']=$social_Account->avatar;
            }

            // $Spskills = Spskill::where('user_id',$profile->user_id)->where('status',1)->get();
            $Spskills = Spskill::with('Skill')->where('user_id',$profile->user_id)->where('status',1)->get();
            if(!empty($Spskills)){
                $j=0;
                foreach ($Spskills as $key => $Spskill) {
                    $result['sp_skills_data'][] = [
                        'sp_skills_id' => $Spskill->id,
                        'sp_skill_images' => "/storage/skills/".$Spskill->skill->avatar,
                        'skill_data' => $Spskill
                    ];
                    $result['sp_skill_images'][$j++]="/storage/skills/".$Spskill->skill->avatar;
                }
            }
            $result['sp_name']=$profile->first_name ." ".$profile->last_name;
            $result['sp_about']=$profile->about;
            $result['sp_slug']=$profile->slug;
            $result['sp_last_login']=$last_login;
            $result['currency']=$profile->currency;
            $result['email']=$profile->email;
            $result['address']=$profile->sp_skill_address ? $profile->sp_skill_address : $profile->address;
            $result['city']=$profile->sp_skill_city ? $profile->sp_skill_city : $profile->city;
            $result['state']=$profile->sp_skill_state ? $profile->sp_skill_state : $profile->state;
            $result['country']=$profile->sp_skill_country ? $profile->sp_skill_country : $profile->country;
            $result['latitude']=$profile->sp_skill_latitude ? $profile->sp_skill_latitude : $profile->latitude;
            $result['longitudes']=$profile->sp_skill_longitudes ? $profile->sp_skill_longitudes : $profile->longitudes;
            $result['phone']=$profile->phone;
            $result['experience']=$profile->experience;
            $result['skillID']=$profile->sID;
            $result['skillname']=$profile->skillname;
            $result['skillavatar']="/storage/skills/".$profile->skillavatar;

            $result['sp_skill_description']=$profile->sp_skill_description;
            $result['sp_skill_price_per_hour']=$profile->sp_skill_price_per_hour;
            $result['sp_skill_price_per_day']=$profile->sp_skill_price_per_day;
            $result['sp_skill_show_price']=__("strings.new.".$profile->sp_skill_show_price);
            $result['sp_skill_offer_discount']=$profile->sp_skill_offer_discount;
            $result['sp_skill_offer_desc']=$profile->sp_skill_offer_desc;
            $result['sp_skill_offer_img']="/storage/spskills/".$profile->sp_skill_offer_img;
            $result['sp_skill_offer_start_date']=$profile->sp_skill_offer_start_date;
            $result['sp_skill_offer_end_date']=$profile->sp_skill_offer_end_date;

            $result[$i]['sp_skill_si_offer']=0;

            // price calculation
            $now = date("Y-m-d");
            $now = strtotime($now);
            $offer_start = strtotime($profile->sp_skill_offer_start_date);
            $offer_end = strtotime($profile->sp_skill_offer_end_date);
            if($profile->sp_skill_show_price =="hour"){
                $final_price = (float)$profile->sp_skill_price_per_hour;
            }else{
                $final_price = (float)$profile->sp_skill_price_per_day;
            }
            $result['normal_price']=$final_price;
            if($offer_start  <= $now  &&  $now  <= $offer_end){
                $final_price = (float)($final_price - (($final_price *$profile->sp_skill_offer_discount)/100 ) );
                $result['sp_skill_si_offer']=1;
            }
            $result['final_price']=$final_price;
            $result['first_name']=$user->first_name;
            $result['last_name']=$user->last_name;

            $recent_profilers[] = $result;
        }

        if( !empty( $recent_profilers ) ) {
            $message =  __('Success!');
            $errors = '';
            $recentProfilersData = $recent_profilers;
        } else {
            $message = '';
            $errors = __('No recent profiles found');
            $recentProfilersData = array();
        }

        $getDefaultAdvertise = Advertisements::with('getSkill')->where('show_in_front_profile', 1)->get();
        // $getads = Advertisements::with('getSkill')->orderBy('id', 'desc')->get();

        return response()->json(
        [
            "status" => 200,
            'errors' => $errors,
            "message" => $message,
            'results' => $recentProfilersData,
            'defaultSponsorads' => $getDefaultAdvertise,
            // 'allSponsorads' => $getads
        ],200);
    }

    /**
     * Get recent ads for mobile app.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function recentProfilersApp(Request $request)
    {
        // try {

        $profilers = DB::table('sp_skill')
        ->select([
            'users.id as user_id',
            'users.first_name',
            'users.last_name',
            'users.email',
            'users.avatar_type',
            'users.avatar_location',
            'users.slug',
            'users.updated_at as updated_at',
            'profile.phone',
            'profile.experience',
            'profile.about',
            'profile.address',
            'profile.city',
            'profile.state',
            'profile.country',
            'profile.pincode',
            'profile.latitude',
            'profile.longitudes',
            'skill.name as skillname',
            'skill.avatar as skillavatar',
            'currency.symbol as currency',
            'sp_skill.id AS sID',
            'sp_skill.tags',
            'sp_skill.description as sp_skill_description',
            'sp_skill.price_per_hour as sp_skill_price_per_hour',
            'sp_skill.price_per_day as sp_skill_price_per_day',
            'sp_skill.show_price as sp_skill_show_price',
            'sp_skill.offer_discount as sp_skill_offer_discount',
            'sp_skill.offer_desc as sp_skill_offer_desc',
            'sp_skill.offer_img as sp_skill_offer_img',
            'sp_skill.offer_start_date as sp_skill_offer_start_date',
            'sp_skill.offer_end_date as sp_skill_offer_end_date',
            'sp_skill.address AS sp_skill_address',
            'sp_skill.city AS sp_skill_city',
            'sp_skill.state AS sp_skill_state',
            'sp_skill.country AS sp_skill_country',
            'sp_skill.pincode AS sp_skill_pincode',
            'sp_skill.latitude AS sp_skill_latitude',
            'sp_skill.longitudes AS sp_skill_longitudes',
            'user_badges.id AS bid',
            'user_badges.badge_name',
            'user_badges.image AS badge_image',
        ])
        ->selectRaw('CASE
        WHEN MONTH(profile_visitors.created_at) = MONTH(CURDATE()) THEN "Most interesting"
        ELSE ""
        END AS interesting_month_status')
        ->leftjoin('users', 'users.id', '=', 'sp_skill.user_id')
        ->leftjoin('profile', 'profile.user_id', '=', 'sp_skill.user_id')
        ->leftjoin('user_badges', 'user_badges.id', '=', 'profile.badge_id')
        ->leftjoin('skill', 'skill.id', '=', 'sp_skill.skill_id')
        ->leftjoin('currency', 'currency.id', '=', 'sp_skill.currency_id')
        ->leftJoin('profile_visitors', 'users.id', '=', 'profile_visitors.visited_user_id')
        ->leftJoin(DB::raw('(SELECT visited_user_id, COUNT(*) as visitor_count FROM profile_visitors GROUP BY visited_user_id) as visitors'), 'users.id', '=', 'visitors.visited_user_id')
        ->where('sp_skill.status', 1)
        ->whereNull('users.deleted_at')
        ->distinct()
        // ->orderBy('sp_skill.id', 'DESC')
        ->inRandomOrder()
        ->take(10)
        ->get();
        // dd(DB::getQueryLog());
        // }catch(Exception $e) {
        //     return response()->json(
        //     [
        //         'errors' => $e->getMessage(),
        //     ],200);
        // }

        $recent_profilers = [];
        foreach($profilers as $i=>$profile) {
            $result = array();

            // User Data
            $userAverageRating = Feedback::user_average_rating($profile->user_id);
            $userAverageRating = round($userAverageRating);
            $user = User::find($profile->user_id);
            $isOnline = $user->isOnline();
            $last_login = $user->updated_at->diffForHumans();
            $result['user_id'] = $profile->user_id;
            $result['rating'] = $userAverageRating;
            $result['badge_id'] = $profile->bid;
            $result['badge_name'] = $profile->badge_name;
            $result['badge_image'] = $profile->badge_image;
            $result['isInterested'] = $profile->interesting_month_status;
            $result['able_to_send_message'] = 0;
            $result['sp_name']=$profile->first_name ." ".$profile->last_name;
            $result['sp_about']=$profile->about;
            $result['sp_slug']=$profile->slug;
            $result['sp_last_login']=$last_login;
            $result['currency']=$profile->currency;
            $result['email']=$profile->email;
            $result['address']=$profile->sp_skill_address ? $profile->sp_skill_address : $profile->address;
            $result['city']=$profile->sp_skill_city ? $profile->sp_skill_city : $profile->city;
            $result['state']=$profile->sp_skill_state ? $profile->sp_skill_state : $profile->state;
            $result['country']=$profile->sp_skill_country ? $profile->sp_skill_country : $profile->country;
            $result['latitude']=$profile->sp_skill_latitude ? $profile->sp_skill_latitude : $profile->latitude;
            $result['longitudes']=$profile->sp_skill_longitudes ? $profile->sp_skill_longitudes : $profile->longitudes;
            $result['phone']=$profile->phone;
            $result['experience']=$profile->experience;

            // User's all skills
            if(!empty($loged)){
                if(!($loged->id == $profile->user_id))
                    $result[$i]['able_to_send_message'] = 1;
            }

            if($profile->avatar_type == "gravatar"){
                $result['sp_image']= "/storage/avatars/dummy.png";
            }else if ($profile->avatar_type == "storage"){
                if($profile->avatar_location){
                    $result['sp_image']="/storage/".$profile->avatar_location;
                } else {
                    $result['sp_image']= "/storage/avatars/dummy.png";
                }
            }else{
                $social_Account = SocialAccount::where('user_id','=',$profile->user_id)->where('provider','=',$profile->avatar_type)->first();
                if(!empty($social_Account))
                    $result['sp_image']=$social_Account->avatar;
            }

            $Spskills = Spskill::with('Skill')->where('user_id',$profile->user_id)->where('status',1)->get();
            if(!empty($Spskills)){
                $j=0;
                foreach ($Spskills as $key => $Spskill) {
                    $result['sp_skills_data'][] = [
                        'sp_skills_id' => $Spskill->id,
                        'sp_skill_images' => "/storage/skills/".$Spskill->skill->avatar,
                        // 'skill_data' => $Spskill
                    ];
                    $result['sp_skill_images'][$j++]="/storage/skills/".$Spskill->skill->avatar;
                    $result['sp_user_all_skills'][$key]['ID'] = $Spskill->id;
                    $result['sp_user_all_skills'][$key]['SID'] = $Spskill->skill_id;
                    $result['sp_user_all_skills'][$key]['s_name'] = $Spskill->skill->name;
                    $result['sp_user_all_skills'][$key]['s_avatar'] = "/storage/skills/".$Spskill->skill->avatar;
                    $result['sp_user_all_skills'][$key]['s_description'] = $Spskill->description;
                    $result['sp_user_all_skills'][$key]['s_price_type'] = $Spskill->show_price;
                    $result['sp_user_all_skills'][$key]['s_price_per_hour'] = $Spskill->price_per_hour;
                    $result['sp_user_all_skills'][$key]['s_price_per_day'] = $Spskill->price_per_day;
                    $result['sp_user_all_skills'][$key]['s_offer_discount'] = $Spskill->offer_discount;
                    $result['sp_user_all_skills'][$key]['s_offer_desc'] = $Spskill->offer_desc;
                    $result['sp_user_all_skills'][$key]['s_offer_img'] = $Spskill->offer_img ? "/storage/spskills/".$Spskill->offer_img : $Spskill->offer_img;
                    $result['sp_user_all_skills'][$key]['s_offer_start_date'] = $Spskill->offer_start_date;
                    $result['sp_user_all_skills'][$key]['s_offer_end_date'] = $Spskill->offer_end_date;
                    $result['sp_user_all_skills'][$key]['s_address'] = $Spskill->address;
                    $result['sp_user_all_skills'][$key]['s_city'] = $Spskill->city;
                    $result['sp_user_all_skills'][$key]['s_state'] = $Spskill->state;
                    $result['sp_user_all_skills'][$key]['s_country'] = $Spskill->country;
                    $result['sp_user_all_skills'][$key]['s_latitude'] = $Spskill->latitude;
                    $result['sp_user_all_skills'][$key]['s_longitudes'] = $Spskill->longitudes;
                    $now = date("Y-m-d");
                    $now = strtotime($now);
                    $offer_start = strtotime($Spskill->offer_start_date);
                    $offer_end = strtotime($Spskill->offer_end_date);
                    if($Spskill->show_price =="hour"){
                        $s_final_price = (float)$Spskill->price_per_hour;
                    }else{
                        $s_final_price = (float)$Spskill->price_per_day;
                    }
                    $result['sp_user_all_skills'][$key]['s_normal_price'] = $s_final_price;
                    if($offer_start  <= $now  &&  $now  <= $offer_end){
                        $s_final_price = (float)($s_final_price - (($s_final_price * $Spskill->offer_discount)/100 ) );
                        $result['sp_user_all_skills'][$key]['s_si_offer'] = 1;
                    } else {
                        $result['sp_user_all_skills'][$key]['s_si_offer'] = 0;
                    }
                    $result['sp_user_all_skills'][$key]['s_final_price'] = $s_final_price;
                }
            }

            $result['skillID']=$profile->sID;
            $result['skillname']=$profile->skillname;
            $result['skillavatar']="/storage/skills/".$profile->skillavatar;

            $result['sp_skill_description']=$profile->sp_skill_description;
            $result['sp_skill_price_per_hour']=$profile->sp_skill_price_per_hour;
            $result['sp_skill_price_per_day']=$profile->sp_skill_price_per_day;
            $result['sp_skill_show_price']=__("strings.new.".$profile->sp_skill_show_price);
            $result['sp_skill_offer_discount']=$profile->sp_skill_offer_discount;
            $result['sp_skill_offer_desc']=$profile->sp_skill_offer_desc;
            $result['sp_skill_offer_img']="/storage/spskills/".$profile->sp_skill_offer_img;
            $result['sp_skill_offer_start_date']=$profile->sp_skill_offer_start_date;
            $result['sp_skill_offer_end_date']=$profile->sp_skill_offer_end_date;

            $result[$i]['sp_skill_si_offer']=0;

            // price calculation
            $now = date("Y-m-d");
            $now = strtotime($now);
            $offer_start = strtotime($profile->sp_skill_offer_start_date);
            $offer_end = strtotime($profile->sp_skill_offer_end_date);
            if($profile->sp_skill_show_price =="hour"){
                $final_price = (float)$profile->sp_skill_price_per_hour;
            }else{
                $final_price = (float)$profile->sp_skill_price_per_day;
            }
            $result['normal_price']=$final_price;
            if($offer_start  <= $now  &&  $now  <= $offer_end){
                $final_price = (float)($final_price - (($final_price *$profile->sp_skill_offer_discount)/100 ) );
                $result['sp_skill_si_offer']=1;
            }
            $result['final_price']=$final_price;
            $result['first_name']=$user->first_name;
            $result['last_name']=$user->last_name;

            $recent_profilers[] = $result;
        }

        if( !empty( $recent_profilers ) ) {
            $message =  __('Success!');
            $errors = '';
            $recentProfilersData = $recent_profilers;
        } else {
            $message = '';
            $errors = __('No recent profiles found');
            $recentProfilersData = array();
        }

        $getDefaultAdvertise = Advertisements::with('getSkill')->where('show_in_front_profile', 1)->get();
        // $getads = Advertisements::with('getSkill')->orderBy('id', 'desc')->get();

        return response()->json(
        [
            "status" => 200,
            'errors' => $errors,
            "message" => $message,
            'results' => $recentProfilersData,
            'defaultSponsorads' => $getDefaultAdvertise,
            // 'allSponsorads' => $getads
        ],200);
    }

    /**
     * Get recent ads for mobile app Latest.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function recentProfilersAppLtst(Request $request)
    {
        // try {
        DB::enableQueryLog();
        $profilers = DB::table('sp_skill')
        ->select([
            'users.id as user_id',
            'users.first_name',
            'users.last_name',
            'users.email',
            'users.avatar_type',
            'users.avatar_location',
            'users.slug',
            'users.updated_at as updated_at',
            'profile.phone',
            'profile.experience',
            'profile.about',
            'profile.address',
            'profile.city',
            'profile.state',
            'profile.country',
            'profile.pincode',
            'profile.latitude',
            'profile.longitudes',
            'skill.name as skillname',
            'skill.avatar as skillavatar',
            'currency.symbol as currency',
            'sp_skill.id AS sID',
            'sp_skill.tags',
            'sp_skill.description as sp_skill_description',
            'sp_skill.price_per_hour as sp_skill_price_per_hour',
            'sp_skill.price_per_day as sp_skill_price_per_day',
            'sp_skill.show_price as sp_skill_show_price',
            'sp_skill.offer_discount as sp_skill_offer_discount',
            'sp_skill.offer_desc as sp_skill_offer_desc',
            'sp_skill.offer_img as sp_skill_offer_img',
            'sp_skill.offer_start_date as sp_skill_offer_start_date',
            'sp_skill.offer_end_date as sp_skill_offer_end_date',
            'sp_skill.address AS sp_skill_address',
            'sp_skill.city AS sp_skill_city',
            'sp_skill.state AS sp_skill_state',
            'sp_skill.country AS sp_skill_country',
            'sp_skill.pincode AS sp_skill_pincode',
            'sp_skill.latitude AS sp_skill_latitude',
            'sp_skill.longitudes AS sp_skill_longitudes',
        ])
        ->selectRaw('CASE
        WHEN MONTH(profile_visitors.created_at) = MONTH(CURDATE()) THEN "Most interesting"
        ELSE ""
        END AS interesting_month_status')
        ->leftjoin('users', 'users.id', '=', 'sp_skill.user_id')
        ->leftjoin('profile', 'profile.user_id', '=', 'sp_skill.user_id')
        ->leftjoin('skill', 'skill.id', '=', 'sp_skill.skill_id')
        ->leftjoin('currency', 'currency.id', '=', 'sp_skill.currency_id')
        ->leftJoin('profile_visitors', 'users.id', '=', 'profile_visitors.visited_user_id')
        ->leftJoin(DB::raw('(SELECT visited_user_id, COUNT(*) as visitor_count FROM profile_visitors GROUP BY visited_user_id) as visitors'), 'users.id', '=', 'visitors.visited_user_id')
        ->where('sp_skill.status', 1)
        ->whereNull('users.deleted_at')
        ->distinct()
        // ->orderBy('sp_skill.id', 'DESC')
        // ->inRandomOrder()
        ->take(10)
        ->get();
        // dd(DB::getQueryLog());
        // }catch(Exception $e) {
        //     return response()->json(
        //     [
        //         'errors' => $e->getMessage(),
        //     ],200);
        // }

        $recent_profilers = [];
        foreach($profilers as $i=>$profile) {
            $result = array();

            // User Data
            $userAverageRating = Feedback::user_average_rating($profile->user_id);
            $userAverageRating = round($userAverageRating);
            $user = User::find($profile->user_id);
            $isOnline = $user->isOnline();
            $last_login = $user->updated_at->diffForHumans();
            $result['user_id'] = $profile->user_id;
            $result['rating'] = $userAverageRating;
            $result['isInterested'] = $profile->interesting_month_status;
            $result['able_to_send_message'] = 0;
            $result['sp_name']=$profile->first_name ." ".$profile->last_name;
            $result['sp_about']=$profile->about;
            $result['sp_slug']=$profile->slug;
            $result['sp_last_login']=$last_login;
            $result['currency']=$profile->currency;
            $result['email']=$profile->email;
            $result['phone']=$profile->phone;
            $result['experience']=$profile->experience;

            // User's all skills
            if(!empty($loged)){
                if(!($loged->id == $profile->user_id))
                    $result[$i]['able_to_send_message'] = 1;
            }

            if($profile->avatar_type == "gravatar"){
                $result['sp_image']= "/storage/avatars/dummy.png";
            }else if ($profile->avatar_type == "storage"){
                if($profile->avatar_location){
                    $result['sp_image']="/storage/".$profile->avatar_location;
                } else {
                    $result['sp_image']= "/storage/avatars/dummy.png";
                }
            }else{
                $social_Account = SocialAccount::where('user_id','=',$profile->user_id)->where('provider','=',$profile->avatar_type)->first();
                if(!empty($social_Account))
                    $result['sp_image']=$social_Account->avatar;
            }

            $Spskills = Spskill::with('Skill')->where('user_id',$profile->user_id)->where('status',1)->get();
            if(!empty($Spskills)){
                $j=0;
                foreach ($Spskills as $key => $Spskill) {
                    $result['sp_skills_data'][] = [
                        'sp_skills_id' => $Spskill->id,
                        'sp_skill_images' => "/storage/skills/".$Spskill->skill->avatar,
                        // 'skill_data' => $Spskill
                    ];
                    $result['sp_skill_images'][$j++]="/storage/skills/".$Spskill->skill->avatar;
                    $result['sp_user_all_skills'][$key]['ID'] = $Spskill->id;
                    $result['sp_user_all_skills'][$key]['SID'] = $Spskill->skill_id;
                    $result['sp_user_all_skills'][$key]['s_name'] = $Spskill->skill->name;
                    $result['sp_user_all_skills'][$key]['s_avatar'] = "/storage/skills/".$Spskill->skill->avatar;
                    $result['sp_user_all_skills'][$key]['s_description'] = $Spskill->description;
                    $result['sp_user_all_skills'][$key]['s_price_type'] = $Spskill->show_price;
                    $result['sp_user_all_skills'][$key]['s_price_per_hour'] = $Spskill->price_per_hour;
                    $result['sp_user_all_skills'][$key]['s_price_per_day'] = $Spskill->price_per_day;
                    $result['sp_user_all_skills'][$key]['s_offer_discount'] = $Spskill->offer_discount;
                    $result['sp_user_all_skills'][$key]['s_offer_desc'] = $Spskill->offer_desc;
                    $result['sp_user_all_skills'][$key]['s_offer_img'] = $Spskill->offer_img ? "/storage/spskills/".$Spskill->offer_img : $Spskill->offer_img;
                    $result['sp_user_all_skills'][$key]['s_offer_start_date'] = $Spskill->offer_start_date;
                    $result['sp_user_all_skills'][$key]['s_offer_end_date'] = $Spskill->offer_end_date;
                    $result['sp_user_all_skills'][$key]['s_address'] = $Spskill->address;
                    $result['sp_user_all_skills'][$key]['s_city'] = $Spskill->city;
                    $result['sp_user_all_skills'][$key]['s_state'] = $Spskill->state;
                    $result['sp_user_all_skills'][$key]['s_country'] = $Spskill->country;
                    $result['sp_user_all_skills'][$key]['s_latitude'] = $Spskill->latitude;
                    $result['sp_user_all_skills'][$key]['s_longitudes'] = $Spskill->longitudes;
                    $now = date("Y-m-d");
                    $now = strtotime($now);
                    $offer_start = strtotime($Spskill->offer_start_date);
                    $offer_end = strtotime($Spskill->offer_end_date);
                    if($Spskill->show_price =="hour"){
                        $s_final_price = (float)$Spskill->price_per_hour;
                    }else{
                        $s_final_price = (float)$Spskill->price_per_day;
                    }
                    $result['sp_user_all_skills'][$key]['s_normal_price'] = $s_final_price;
                    if($offer_start  <= $now  &&  $now  <= $offer_end){
                        $s_final_price = (float)($s_final_price - (($s_final_price * $Spskill->offer_discount)/100 ) );
                        $result['sp_user_all_skills'][$key]['s_si_offer'] = 1;
                    } else {
                        $result['sp_user_all_skills'][$key]['s_si_offer'] = 0;
                    }
                    $result['sp_user_all_skills'][$key]['s_final_price'] = $s_final_price;
                }
            }

            $result['skillID']=$profile->sID;
            $result['skillname']=$profile->skillname;
            $result['skillavatar']="/storage/skills/".$profile->skillavatar;

            $result['sp_skill_description']=$profile->sp_skill_description;
            $result['sp_skill_price_per_hour']=$profile->sp_skill_price_per_hour;
            $result['sp_skill_price_per_day']=$profile->sp_skill_price_per_day;
            $result['sp_skill_show_price']=__("strings.new.".$profile->sp_skill_show_price);
            $result['sp_skill_offer_discount']=$profile->sp_skill_offer_discount;
            $result['sp_skill_offer_desc']=$profile->sp_skill_offer_desc;
            $result['sp_skill_offer_img']="/storage/spskills/".$profile->sp_skill_offer_img;
            $result['sp_skill_offer_start_date']=$profile->sp_skill_offer_start_date;
            $result['sp_skill_offer_end_date']=$profile->sp_skill_offer_end_date;

            $result[$i]['sp_skill_si_offer']=0;

            // price calculation
            $now = date("Y-m-d");
            $now = strtotime($now);
            $offer_start = strtotime($profile->sp_skill_offer_start_date);
            $offer_end = strtotime($profile->sp_skill_offer_end_date);
            if($profile->sp_skill_show_price =="hour"){
                $final_price = (float)$profile->sp_skill_price_per_hour;
            }else{
                $final_price = (float)$profile->sp_skill_price_per_day;
            }
            $result['normal_price']=$final_price;
            if($offer_start  <= $now  &&  $now  <= $offer_end){
                $final_price = (float)($final_price - (($final_price *$profile->sp_skill_offer_discount)/100 ) );
                $result['sp_skill_si_offer']=1;
            }
            $result['final_price']=$final_price;
            $result['first_name']=$user->first_name;
            $result['last_name']=$user->last_name;

            $recent_profilers[] = $result;
        }

        if( !empty( $recent_profilers ) ) {
            $message =  __('Success!');
            $errors = '';
            $recentProfilersData = $recent_profilers;
        } else {
            $message = '';
            $errors = __('No recent profiles found');
            $recentProfilersData = array();
        }

        $getDefaultAdvertise = Advertisements::with('getSkill')->where('show_in_front_profile', 1)->get();
        // $getads = Advertisements::with('getSkill')->orderBy('id', 'desc')->get();

        return response()->json(
        [
            "status" => 200,
            'errors' => $errors,
            "message" => $message,
            'results' => $recentProfilersData,
            'defaultSponsorads' => $getDefaultAdvertise,
            // 'allSponsorads' => $getads
        ],200);
    }


    /**
     * Get recent ads for mobile app.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getCurrencyApp(Request $request)
    {
        $Currency = Currency::orderBy('id', 'desc')->latest()->get();

        if( $Currency ) {
            return response()->json(
            [
                "status" => 200,
                'errors' => '',
                "message" => "Curency data.",
                'results' => $Currency
            ],200);
        } else {
            return response()->json(
            [
                "status" => 200,
                'errors' => '',
                "message" => "No Curency data.",
                'results' => ''
            ],200);
        }
    }
}
