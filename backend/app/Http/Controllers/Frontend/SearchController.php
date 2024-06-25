<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Skill;
use App\Models\Spskill;
use App\Models\Auth\User;
use App\Models\Feedback;
use App\Models\UserAds;
use App\Models\Advertisements;
use App\Models\ProfileVisitors;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function searchsp(Request $request)
    {
        //
        $validation = Validator::make($request->all(), [
            'skillid'   => 'required|exists:skill,id,deleted_at,NULL',
        ]);

        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()]);
        }

        if( !empty( $request->skillid ) ) {
            $skillid    = $request->skillid;
            $all_tag    = array($skillid);
        } else {
            $allSkills = Skill::all();
            $newSkillsId = array();
            foreach( $allSkills as $skeys => $skill ) {
                $newSkillsId[] = $skill;
            }
            $skillid = $newSkillsId;
            $all_tag = $skillid;
        }

        $skillname = $request->skillname;
        $where = $request->where;
        $street_number = $request->street_number;
        $route = $request->route;
        $city = $request->locality;
        $state = $request->administrative_area_level_1;
        $country = $request->country;
        $postal_code = $request->postal_code;
        $lng = $request->lng;
        $lat = $request->lat;
        $sort_=  $request->sort_by;
        $location['latitude'] = $lat;
        $location['longitude'] = $lng;
        $setting = array();
        $ratings = $request->rating;
        $profile_badge = $request->profile_badge;
        $my_results = [];

        if( !empty( $all_tag ) ){
            $sp_skills_data = DB::table('sp_skill')->select('sp_skill.*')->where(['status' => 1]);
            $sp_skills_data->where(function ($main_query) use($all_tag){
                $main_query->orWhereIn('sp_skill.skill_id', function ($query) use ($all_tag) {
                    $query->select('id')->from('skill')->where('id', $all_tag);
                });
            });
            $sp_skills_data->whereIn('sp_skill.user_id', function($query){
                $query->select('id')->from('users')->where('is_sp', 1)->where('active', 1);
            });

            if( !empty($city) ){
                $sp_skills_data->where('sp_skill.city', 'LIKE','%'.$city.'%');
            }
            if( !empty($country) ){
                $sp_skills_data->where('sp_skill.country', 'LIKE','%'.$country.'%');
            }

            if( $sp_skills_data->paginate()->count() == 0 && !empty($city) ){
                $sp_new = DB::table('sp_skill')->select('sp_skill.*')->where(['status' => 1]);
                if( !empty($ratings) && $ratings!="null") {
                    $sp_new->leftJoin('overall_profile_ratings', 'overall_profile_ratings.uid', '=', 'sp_skill.user_id');
                    $sp_new->where('overall_profile_ratings.total_rating', $ratings);
                }

                if( !empty( $profile_badge ) && $profile_badge ) {
                    $sp_new->leftJoin('profile', 'profile.user_id', '=', 'sp_skill.user_id');
                    $sp_new->where('profile.badge_id', '=', $profile_badge);
                }

                $sp_new->where(function ($main_query) use($all_tag){
                    $main_query->orWhereIn('sp_skill.skill_id', function ($query) use ($all_tag) {
                        $query->select('id')->from('skill')->where('id', $all_tag);
                    });
                });
                $sp_new->whereIn('sp_skill.user_id', function($query){
                    $query->select('id')->from('users')->where('is_sp', 1)->where('active', 1);
                });
                if( !empty($location['latitude']) && !empty($location['longitude']) ) {
                    $haversine = "(6371 * acos(cos(radians(".$location['latitude'].")) * cos(radians(latitude)) * cos(radians(longitudes) - radians(".$location['longitude'].")) + sin(radians(".$location['latitude'].")) * sin(radians(latitude))))";
                    $sp_new->whereRaw("{$haversine} < ?", [60]);
                }
                $my_results[] = $sp_new->orderBy('sp_skill.id', 'desc')->get();
                // return response()->json(["status" => 200, "data from IF" => $my_results]);
            } else {
                DB::enableQueryLog();
                if( !empty($ratings) && $ratings!="null") {
                    $sp_skills_data->leftJoin('overall_profile_ratings', 'overall_profile_ratings.uid', '=', 'sp_skill.user_id');
                    $sp_skills_data->where('overall_profile_ratings.total_rating', $ratings);
                }
                if( !empty( $profile_badge ) ) {
                    $sp_skills_data->leftjoin('profile', 'profile.user_id', '=', 'sp_skill.user_id');
                    $sp_skills_data->where('profile.badge_id', '=', $profile_badge);
                }

                if( !empty($location['latitude']) && !empty($location['longitude']) ) {
                    $haversine = "(6371 * acos(cos(radians(".$location['latitude'].")) * cos(radians(sp_skill.latitude)) * cos(radians(sp_skill.longitudes) - radians(".$location['longitude'].")) + sin(radians(".$location['latitude'].")) * sin(radians(sp_skill.latitude))))";
                    $sp_skills_data->whereRaw("{$haversine} < ?", [60]);
                }
                $my_results[] = $sp_skills_data->orderBy('sp_skill.id', 'desc')->get();
                // return response()->json(["status" => 200, "data from Else" => $my_results]);
            }
        }

        $return = [];
        $processedUsers = [];

        foreach ($my_results as $k => $datas) {
            $setting[$k]['total'] = count($datas);
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
            $return[] = $result;
        }

        $getads = '';
        if( !empty( $request->locality ) ) {

            if( !empty($request->skillid) || $request->skillid != '') {
                $skillid    = $request->skillid;
                $all_tags    = explode(',', $skillid);
                $allSkilsArr = [];
                foreach ($all_tags as $key => $skill) {
                    // code...
                    $allSkilsArr[] = $skill;
                }
                // dd($allSkilsArr);
                $getads = Advertisements::with('getSkill')->where('city', $request->locality)->whereIn('skill_id', $allSkilsArr)->orderBy('id', 'desc')->get();
            } else {
                $getads = Advertisements::with('getSkill')->where('city', $request->locality)->orderBy('id', 'desc')->get();
            }
        }

        if( !empty($return) ){
            $message =  __('Success');
            $errors = array();
            $resultdata = array(
                'results' => $return,
                'settings' => $setting,
                // 'defaultSponsorAds' => $getDefaultAdvertise,
                'allSponsorsAds' => $getads
            );
        } else {
            $message = array();
            $errors = __('Whoops, looks like something went wrong');
            $resultdata = array();
        }

        return response()->json(
        [
            'errors' => $errors,
            "status" => 200,
            "message" => $message,
            "data" => $resultdata,
        ],200);
        // return response()->json(["status" => 200, "data" => $my_results]);
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
