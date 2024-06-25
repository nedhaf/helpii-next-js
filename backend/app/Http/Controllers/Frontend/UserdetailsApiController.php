<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\Auth\User;
use App\Models\Skill;
use App\Models\Spskill;
use App\Models\Spavailability;
use App\Models\Profile;
use App\Models\Currency;
use App\Models\Feedback;
use App\Models\ProfileVisitors;
use App\Models\OverallProfileRating;
use App\Models\FavSp;
use App\Models\UserAds;
use App\Models\Sitesettings;
use App\Models\UserBadge;


class UserdetailsApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'slug'   => 'required|exists:users,slug',
        ]);
        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()]);
        }
        $uid = $request->slug;
        $userData = User::where('slug',$uid)->first();
        if( !empty($userData) ) {
            $userId=$userData->id;
            $spavtar = array();
            if( auth()->Id() != $userId ) {
                $visitorArr = [
                    'visited_user_id' => $userId,
                    'user_id' => auth()->Id()
                ];
                // $storeVisitor = ProfileVisitors::create($visitorArr);
            }
            $userStatus=$userData->active;
            if( $userStatus ) {
                $alluserData = User::with(['Profile', 'favouriteusers'])->where('id', '=', $userId)->first();

                $feedbackData = Feedback::with('Skill', 'SpSkill')->leftjoin('users', 'users.id', '=', 'rating.from_userid')
                ->leftJoin('social_accounts', function($join){
                    $join->on('social_accounts.user_id', '=', 'rating.from_userid')
                    ->on('users.avatar_type', '=', 'social_accounts.provider');
                })->select('rating.*','users.first_name','users.last_name','users.avatar_type','users.avatar_location','users.email','social_accounts.provider','social_accounts.avatar')
                ->selectRaw('JSON_LENGTH(JSON_EXTRACT(user_liked, "$.user_id")) AS user_like_count')
                ->selectRaw('JSON_LENGTH(JSON_EXTRACT(user_disliked, "$.user_id")) AS user_dislike_count')
                ->where('rating.to_userid', $userId)
                ->orderBy('rating.id', 'DESC')
                ->get();
                $feedbacklikedUsers = array();

                if( !empty($feedbackData) ) {
                    foreach ($feedbackData as $key => $likedUser) {
                        $feedbacklikedUsers[$key] = $likedUser;
                        $likedUserDataArr = array();

                        if (!empty($likedUser->user_liked)) {
                            $likedUsersData = json_decode($likedUser->user_liked, true);
                            foreach ($likedUsersData['user_id'] as $k => $likedUserD) {
                                $LUData = User::where('id', $likedUserD)->first();
                                $user_image     =   "/storage/avatars/dummy.png";
                                if( $LUData->avatar_type == "gravatar" ) {
                                    $user_image     =   "/storage/avatars/dummy.png";
                                } else if ( $LUData->avatar_type == "storage" ) {
                                    if( !empty( $LUData->avatar_location ) ){
                                        $user_image =   "/storage/".$LUData->avatar_location;
                                    } else {
                                        $user_image =   "/storage/avatars/dummy.png";
                                    }

                                } else{
                                    $social_Account = DB::table('social_accounts')->where('user_id','=',$LUData->user_id)->where('provider','=',$LUData->avatar_type)->first();
                                    if(!empty($social_Account)){
                                        $user_image =   $social_Account->avatar;
                                    } else {
                                        $user_image =   "/storage/avatars/dummy.png";
                                    }
                                }
                                $likedUserDataArr[] = [
                                    'user_id' => $LUData->id,
                                    'name' => $LUData->first_name . ' ' . $LUData->last_name,
                                    'email' => $LUData->email,
                                    'avatar_type' => $LUData->avatar_type,
                                    'avatar_image' => $user_image,
                                    'like_type' => 'isLiked',
                                ];
                            }
                        }

                        // Dislike
                        if (!empty($likedUser->user_disliked)) {
                            $dislikedUsersData = json_decode($likedUser->user_disliked, true);
                            foreach ($dislikedUsersData['user_id'] as $k => $dlikedUserD) {
                                $DLUData = User::where('id', $dlikedUserD)->first();
                                $user_image     =   "/storage/avatars/dummy.png";
                                if( $DLUData->avatar_type == "gravatar" ) {
                                    $user_image     =   "/storage/avatars/dummy.png";
                                } else if ( $DLUData->avatar_type == "storage" ) {
                                    if( !empty( $DLUData->avatar_location ) ){
                                        $user_image =   "/storage/".$DLUData->avatar_location;
                                    } else {
                                        $user_image =   "/storage/avatars/dummy.png";
                                    }

                                } else{
                                    $social_Account = DB::table('social_accounts')->where('user_id','=',$DLUData->user_id)->where('provider','=',$DLUData->avatar_type)->first();
                                    if(!empty($social_Account)){
                                        $user_image =   $social_Account->avatar;
                                    } else {
                                        $user_image =   "/storage/avatars/dummy.png";
                                    }
                                }

                                $likedUserDataArr[] = [
                                    'user_id' => $DLUData->id,
                                    'name' => $DLUData->first_name . ' ' . $DLUData->last_name,
                                    'email' => $DLUData->email,
                                    'avatar_type' => $DLUData->avatar_type,
                                    'avatar_image' => $user_image,
                                    'like_type' => 'isDisliked',
                                ];
                            }
                        }
                        $feedbacklikedUsers[$key]->reactions = $likedUserDataArr;
                    }
                }
                $spskill_id=array();
                // $Sproskills = DB::table('sp_skill')->where('user_id','=',$userId)->where('status','=',1)->get();
                $Sproskills = Spskill::with('Skill')->where('user_id','=',$userId)->where('status','=',1)->get();
                if(!empty($Sproskills)) {
                    foreach ($Sproskills as $sskill) {
                       $spskill_id[]=$sskill->skill_id;
                   }
                }
                if( empty($alluserData->Spavailability) || count($alluserData->Spavailability) < 0 ) {
                    $alluserData['spavailabilityNew'] = "{\"monday\":{\"close\":1},\"tuesday\":{\"close\":1},\"wednesday\":{\"close\":1},\"thursday\":{\"close\":1},\"friday\":{\"close\":1},\"saturday\":{\"close\":1},\"sunday\":{\"close\":1}}";
                }
                $user = Auth::user();
                $checkonline=0;
                $isonline = 0;
                if(!empty($checkonline)) {
                    $isonline = 1;
                }
                $fromuserData = 0;
                if((!empty($user)) && (!empty($spskill_id))) {
                    $fromuserId=$user->id;
                    $fromuserData = Feedback::CheckRemainigSkill($userId,$fromuserId);
                }
                $userAverageRating = Feedback::user_average_rating($userId);
                $userAverageRating = round($userAverageRating);
                // $userAverageRating = 0;
                // $userAverageRating = round($userAverageRating);

                $sp_fav=0;

                if(!empty($user)){
                    $FavSp  = FavSp::where('user_id','=',$user->id)->where('fav_user_id','=',$userId)->first();

                    if(!empty($FavSp)){
                        $sp_fav=1;
                    }
                }

                $advtisement = DB::table('ads')->where('pagename','userdetailspage')->where('status',1)->get()->toArray();
                $ads=array();
                foreach ($advtisement as $key => $value) {
                    switch ($value['position']) {
                        case 'top':
                        $ads['top']=$value;
                        break;
                        case 'bottom':
                        $ads['bottom']=$value;
                        break;

                        case 'left':
                        $ads['left']=$value;
                        break;

                        case 'right':
                        $ads['right']=$value;
                        break;
                    }
                }
                $FavSp      = FavSp::where('fav_user_id', $alluserData->id)->get();
                $UserAdsDataArr = array();
                // $user_ads = DB::table('user_ads')->leftJoin('skill', function($join){
                //     $join->on('skill.id', '=', 'user_ads.skill_id');
                // })->select('user_ads.*','skill.name', 'skill.avatar')
                // ->where('user_ads.user_id', $alluserData->id)->where('user_ads.status', 1)->get();
                $user_ads = UserAds::with(['getCurrency', 'getAdsSkill'])->where('user_ads.user_id', $alluserData->id)->where('user_ads.status', 1)->get();
                $skills = Spskill::with(['Skill','currency'])->where('user_id', $alluserData->id)->get();

                $sitesettingsData = Sitesettings::get();
                $sitesettingsData = reset($sitesettingsData);

                // $getCurrentMonthVisitors = DB::table('profile_visitors')
                //     ->whereMonth('created_at', '=', date('m'))
                //     ->whereYear('created_at', '=', date('Y'))
                //     ->where('visited_user_id', $uid)
                //     ->count();
                $getCurrentMonthVisitors = ProfileVisitors::query()
                    ->whereMonth('created_at', '=', date('m'))
                    ->whereYear('created_at', '=', date('Y'))
                    ->where('visited_user_id', $alluserData->id)
                    ->count();

                $currentYearWeek = date('oW');
                $getCurrentWeekVisitors = ProfileVisitors::query()
                ->whereRaw("YEARWEEK(created_at) = $currentYearWeek")
                ->where('visited_user_id', $alluserData->id)
                ->count();

                $FavTotal      = FavSp::where('fav_user_id', $alluserData->id)->count();

                $visitorArr = [
                    'current_month' => $getCurrentMonthVisitors,
                    'current_week' => $getCurrentWeekVisitors,
                    'totalFav' => $FavTotal
                ];
                $userBadge = array();
                if( !empty( $alluserData->profile ) ) {

                    $badges = UserBadge::where('id', $alluserData->profile->badge_id)->first();
                    if( !empty($badges) ) {
                        $userBadge[] = $badges;
                    }
                }
                // Get Auth favorites users
                // $getFavorites = array();
                // if( Auth::user()->id == $userId ) {
                //     $datas = FavSp::select([ 'users.id as user_id', 'users.first_name', 'users.last_name', 'users.email', 'users.avatar_type', 'users.avatar_location', 'users.slug', 'users.updated_at as updated_at', 'profile.phone', 'profile.experience', 'profile.about', 'profile.address', 'profile.city', 'profile.state', 'profile.country', 'profile.pincode', 'profile.latitude', 'profile.longitudes', 'skill.name as skillname', 'skill.avatar as skillavatar', 'currency.symbol as currency', 'sp_skill.id AS SpId', 'sp_skill.tags', 'sp_skill.description as sp_skill_description', 'sp_skill.price_per_hour as sp_skill_price_per_hour', 'sp_skill.price_per_day as sp_skill_price_per_day', 'sp_skill.show_price as sp_skill_show_price', 'sp_skill.offer_discount as sp_skill_offer_discount', 'sp_skill.offer_desc as sp_skill_offer_desc', 'sp_skill.offer_img as sp_skill_offer_img', 'sp_skill.offer_start_date as sp_skill_offer_start_date', 'sp_skill.offer_end_date as sp_skill_offer_end_date', 'sp_skill.address AS sp_skill_address', 'sp_skill.city AS sp_skill_city', 'sp_skill.state AS sp_skill_state', 'sp_skill.country AS sp_skill_country', 'sp_skill.pincode AS sp_skill_pincode', 'sp_skill.latitude AS sp_skill_latitude', 'sp_skill.longitudes AS sp_skill_longitudes',])
                //     ->leftjoin('profile', 'profile.user_id', '=', 'fav_sp.fav_user_id')
                //     ->leftjoin('users', 'users.id', '=', 'fav_sp.fav_user_id')
                //     ->leftjoin('sp_skill', 'sp_skill.user_id', '=', 'fav_sp.fav_user_id')
                //     ->leftjoin('skill', 'skill.id', '=', 'fav_sp.fav_user_id')
                //     ->leftjoin('currency', 'currency.id', '=', 'sp_skill.currency_id')
                //     ->where('fav_sp.user_id', '=',Auth::user()->id)
                //     ->orderBy('users.id', 'DESC')
                //     ->get();
                // }
                return response()->json(
                [
                    'errors' => '',
                    "status" => 200,
                    "message" => "Success",
                    "isOnlineUser" => $isonline,
                    "profile_badge" => $userBadge,
                    "current_user_skills" => $Sproskills,
                    "alluserData" => $alluserData,
                    "feedback_data" => $feedbacklikedUsers,
                    "userAverageRating" => $userAverageRating,
                    "UserAds" => $user_ads,
                    "skills" => $skills,
                    "sitesettingsData" => $sitesettingsData,
                    "visitors_results" => $visitorArr,
                    "null_spavailability" => "{\"monday\":{\"from\":null,\"to\":null,\"close\":\"1\"},\"tuesday\":{\"from\":null,\"to\":null,\"close\":\"1\"},\"wednesday\":{\"from\":null,\"to\":null,\"close\":\"1\"},\"thursday\":{\"from\":null,\"to\":null,\"close\":\"1\"},\"friday\":{\"from\":null,\"to\":null,\"close\":\"1\"},\"saturday\":{\"from\":null,\"to\":null,\"close\":\"1\"},\"sunday\":{\"from\":null,\"to\":null,\"close\":\"1\"}}"
                ],200);
            } else {
                return response()->json(['errors' => 'No data available']);
            }
        } else {
            return response()->json(['errors' => 'No data available']);
        }
    }

    public function userDetails($slug)
    {
        // return response()->json(['errors' => $slug]);
        // $validation = Validator::make($slug, [
        //     'slug'   => 'required|exists:users,slug',
        // ]);
        // if ($validation->fails()) {
        //     return response()->json(['errors' => $validation->errors()]);
        // }
        $uid = $slug;
        $userData = User::where('slug',$uid)->first();
        if( !empty($userData) ) {
            $userId=$userData->id;
            $spavtar = array();
            if( auth()->Id() != $userId ) {
                $visitorArr = [
                    'visited_user_id' => $userId,
                    'user_id' => auth()->Id()
                ];
                $storeVisitor = ProfileVisitors::create($visitorArr);
            }
            $userStatus=$userData->active;
            if( $userStatus ) {
                $alluserData = User::with(['Profile'])->where('id', '=', $userId)->first();

                $feedbackData = DB::table('rating')->leftjoin('users', 'users.id', '=', 'rating.from_userid')
                ->leftJoin('social_accounts', function($join){
                    $join->on('social_accounts.user_id', '=', 'rating.from_userid')
                    ->on('users.avatar_type', '=', 'social_accounts.provider');
                })->select('rating.*','users.first_name','users.last_name','users.avatar_type','users.avatar_location','users.email','social_accounts.provider','social_accounts.avatar')
                ->selectRaw('JSON_LENGTH(JSON_EXTRACT(user_liked, "$.user_id")) AS user_like_count')
                ->selectRaw('JSON_LENGTH(JSON_EXTRACT(user_disliked, "$.user_id")) AS user_dislike_count')
                ->where('rating.to_userid', $userId)
                ->orderBy('rating.id', 'DESC')
                ->get();
                $feedbacklikedUsers = array();

                return response()->json(
                [
                    'errors' => '',
                    "status" => 200,
                    "message" => "Success",
                    "alluserData" => $alluserData,

                ],200);
            } else {
                return response()->json(['errors' => 'No data available']);
            }
        } else {
            return response()->json(['errors' => 'No data available']);
        }
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

    public function updateLanguage(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'country'   => 'required',
        ]);
        if ($validation->fails()) {
            return response()->json([
                'status' => 403,
                'errors' => $validation->errors(),
                'msg' => null
            ]);
        }

        $user = Auth::user()->id;
        $getUserProfile = Profile::where('user_id', $user)->first();
        $getUserProfile->language = $request->country;

        if( $getUserProfile->save() ) {
            return response()->json([
                'status' => 200,
                'errors' => null,
                'msg' => 'Language updated successfully.',
                'data' => $user
            ], 200);
        } else {
            return response()->json([
                'status' => 403,
                'errors' => 'Oops! Something wrong please try agian.',
                'msg' => null
            ], 200);
        }
    }

    public function updateDetails(Request $request)
    {
        // return response()->json([
        //     'res' => $request->FirstName,
        // ]);
        // $validation = Validator::make($request->all(), [
        //     'FirstName'   => 'required',
        //     'LastName'   => 'required',
        //     'Mobile'   => 'required',
        //     'LinkedIn'   => 'nullable',
        //     'Insta'   => 'nullable',
        //     'Facebook'   => 'nullable',
        // ]);
        // if ($validation->fails()) {
        //     return response()->json([
        //         'status' => 403,
        //         'errors' => $validation->errors(),
        //         'msg' => null
        //     ]);
        // }

        $user = Auth::user()->id;
        $getUserProfile = Profile::where('user_id', $user)->first();
        $getUser = User::with('profile')->where('id', $user)->first();

        $getUser->first_name = $request->FirstName;
        $getUser->last_name = $request->LastName;

        $getUserProfile->phone = $request->Mobile ? $request->Mobile : null;
        $getUserProfile->linkedin = $request->LinkedIn ? $request->LinkedIn : null;
        $getUserProfile->facebook = $request->Facebook ? $request->Facebook : null;
        $getUserProfile->instagram = $request->Insta ? $request->Insta : null;

        if($getUser->save() && $getUserProfile->save()) {
            $message = __('Profile details updated successfully!');
            $errors = null;
        } else {
            $message = null;
            $errors = __('Profile details not update!');
        }

        return response()->json(
        [
            "status" => 200,
            'errors' => $errors,
            "message" => $message,
            "user" => $getUser
        ],200);
    }

    public function updateCurrency(Request $request)
    {
        $user = Auth::user()->id;
        $getUserProfile = Profile::where('user_id', $user)->first();
        if( !empty($getUserProfile) ) {

            $getUserProfile->currency_id = $request->currency_id;

            if( $getUserProfile->save() ){
                $Currency = Currency::where('id', $getUserProfile->currency_id)->first();

                $message = __('Currecny updated successfully!');
                $errors = null;
                $currency = $Currency;
            } else {
                $message = __('Currency not updated successfully!');
                $errors = null;
                $currency = null;
            }
            return response()->json(
            [
                "status" => 200,
                'errors' => $errors,
                "message" => $message,
                "currency" => $currency,
            ],200);
        } else {
            return response()->json([
                'res' => $getUserProfile,
            ]);
        }
    }
}
