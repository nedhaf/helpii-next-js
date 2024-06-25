<?php

namespace App\Http\Controllers\Api\Frontend\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Auth\User;
use App\Models\ProfileVisitors;
use App\Models\Spskill;
use App\Models\Spavailability;
use App\Models\Photogallary;
use App\Models\Skill;
use Illuminate\Support\Facades\Auth;
use App\Models\Feedback;
use App\Models\OverallProfileRating;
use App\Models\FavSp;
use App\Models\Ads;
use App\Models\UserAds;
use App\Models\UserNotifications;
use App\Models\Sitesettings;
use App\Models\Auth\PasswordHistory;
use App\Models\Message;
use App\Models\UserBadge;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UserdetailsApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        // dd(auth('sanctum')->check());
        if( auth('sanctum')->check() ) {

            $validation = Validator::make($request->all(), [
                'uid'   => 'required|exists:users,id',
            ]);
            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()]);
            }
            $uid = $request->uid;
            $userData = User::where('id',$uid)->first();
            if( !empty($userData) ) {
                $userId=$userData->id;
                $spavtar = array();
                // $alluserData = array();
               if( auth()->Id() != $userId ) {
                    $visitorArr = [
                        'visited_user_id' => $uid,
                        'user_id' => auth()->Id()
                    ];
                    $storeVisitor = ProfileVisitors::create($visitorArr);
                }
                $userStatus=$userData->active;
                if( $userStatus ) {
                    $alluserData = User::with(['Profile','SocialAccount','Spavailability','Photogallary'])->where('id', '=', $userId)->first();

                    $feedbackData = DB::table('rating')->leftjoin('users', 'users.id', '=', 'rating.from_userid')
                    ->leftJoin('social_accounts', function($join){
                        $join->on('social_accounts.user_id', '=', 'rating.from_userid')
                        ->on('users.avatar_type', '=', 'social_accounts.provider');
                    })
                    ->select('rating.*','users.first_name','users.last_name','users.avatar_type','users.avatar_location','users.email','social_accounts.provider','social_accounts.avatar')
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
                                        $social_Account = SocialAccount::where('user_id','=',$LUData->user_id)->where('provider','=',$LUData->avatar_type)->first();
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
                                        $social_Account = SocialAccount::where('user_id','=',$DLUData->user_id)->where('provider','=',$DLUData->avatar_type)->first();
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
                    $Sproskills = Spskill::with('Skill')->where('user_id','=',$userId)->where('status','=',1)->get();
                    if(!empty($Sproskills)) {
                        foreach ($Sproskills as $sskill) {
                           $spskill_id[]=$sskill->skill_id;
                       }
                    }

                    if( empty($alluserData->Spavailability) || count($alluserData->Spavailability) < 0 ) {
                        $alluserData['spavailabilityNew'] = "{\"monday\":{\"close\":1},\"tuesday\":{\"close\":1},\"wednesday\":{\"close\":1},\"thursday\":{\"close\":1},\"friday\":{\"close\":1},\"saturday\":{\"close\":1},\"sunday\":{\"close\":1}}";
                    }
                   // $sessflag = $request->session()->get('flag');
                   // $flag = '';
                   //  if($sessflag) {
                   //      $flag = 'feedback';
                   //      $request->session()->forget('flag');
                   //  }
                    $user = Auth::user();
                    $checkonline=$userData->isOnline();
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

                    $sp_fav=0;

                    if(!empty($user)){
                        $FavSp  = FavSp::where('user_id','=',$user->id)->where('fav_user_id','=',$userId)->first();

                        if(!empty($FavSp)){
                            $sp_fav=1;
                        }
                    }

                    $advtisement = Ads::where('pagename','userdetailspage')->where('status',1)->get()->toArray();
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

                    $model      = new Feedback;
                    $FavSp      = FavSp::where('fav_user_id', $alluserData->id)->get();

                    $UserAdsDataArr = array();
                    $user_ads = UserAds::with(['getCurrency', 'getAdsSkill'])->where('user_ads.user_id', $alluserData->id)->where('user_ads.status', 1)->get();

                    $skills = Spskill::with(['Skill','currency'])->where('user_id', $alluserData->id)->get();

                    $sitesettingsData = Sitesettings::get();
                    $sitesettingsData = reset($sitesettingsData);

                    $getCurrentMonthVisitors = ProfileVisitors::query()
                    ->whereMonth('created_at', '=', date('m'))
                    ->whereYear('created_at', '=', date('Y'))
                    ->where('visited_user_id', $uid)
                    ->count();

                    $currentYearWeek = date('oW');
                    $getCurrentWeekVisitors = ProfileVisitors::query()
                    ->whereRaw("YEARWEEK(created_at) = $currentYearWeek")
                    ->where('visited_user_id', $uid)
                    ->count();

                    $FavTotal      = FavSp::where('fav_user_id', $uid)->count();

                    $visitorArr = [
                        'current_month' => $getCurrentMonthVisitors,
                        'current_week' => $getCurrentWeekVisitors,
                        'totalFav' => $FavTotal
                    ];

                    // Profile badges
                    // dd($alluserData->profile);
                    $userBadge = array();
                    if( !empty( $alluserData->profile ) ) {

                        $badges = UserBadge::where('id', $alluserData->profile->badge_id)->first();
                        if( !empty($badges) ) {
                            $userBadge[] = $badges;
                        }
                    }


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
        } else {
            return response()->json(['errors' => 'User is not logged in'],401);
        }
    }

    /**
     * For testing
     *
     * @return \Illuminate\Http\Response
     */
    public function getProfileDOrg(Request $request)
    {
        if( auth('sanctum')->check() ) {

            $validation = Validator::make($request->all(), [
                'uid'   => 'required|exists:users,id',
            ]);
            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()]);
            }
            $uid = $request->uid;
            $userData = User::where('id',$uid)->first();
            if( !empty($userData) ) {
                $userId=$userData->id;
                $spavtar = array();
                // $alluserData = array();
                // $visitors = new ProfileVisitors;
                if( auth()->Id() != $userId ) {
                    $visitorArr = [
                        'visited_user_id' => $uid,
                        'user_id' => auth()->Id()
                    ];
                    $storeVisitor = ProfileVisitors::create($visitorArr);
                }
                $userStatus=$userData->active;
                if( $userStatus ) {
                    $alluserData = User::with(['Profile','SocialAccount','Spavailability','Photogallary'])->where('id', '=', $userId)->first();

                    $feedbackData = DB::table('rating')->leftjoin('users', 'users.id', '=', 'rating.from_userid')
                    ->leftJoin('social_accounts', function($join){
                        $join->on('social_accounts.user_id', '=', 'rating.from_userid')
                        ->on('users.avatar_type', '=', 'social_accounts.provider');
                    })
                    ->select('rating.*','users.first_name','users.last_name','users.avatar_type','users.avatar_location','users.email','social_accounts.provider','social_accounts.avatar')
                    ->selectRaw('JSON_LENGTH(JSON_EXTRACT(user_liked, "$.user_id")) AS user_like_count')
                    ->selectRaw('JSON_LENGTH(JSON_EXTRACT(user_disliked, "$.user_id")) AS user_dislike_count')
                    ->where('rating.to_userid', $userId)
                    ->orderBy('rating.id', 'DESC')
                    ->get();

                    // $feedbacklikedUsers = array();

                    // foreach ($feedbackData as $key => $likedUser) {
                    //     $feedbacklikedUsers[$key] = $likedUser;
                    //     if (!empty($likedUser->user_liked)) {
                    //         $likedUsersData = json_decode($likedUser->user_liked, true);
                    //         $likedUserDataArr = array();
                    //         foreach ($likedUsersData['user_id'] as $k => $likedUserD) {
                    //             $LUData = User::where('id', $likedUserD)->first();
                    //             $user_image     =   "/storage/avatars/dummy.png";
                    //             $likedUserDataArr[$k]['id'] = $LUData->id;
                    //             $likedUserDataArr[$k]['name'] = $LUData->first_name.' '.$LUData->last_name;
                    //             $likedUserDataArr[$k]['email'] = $LUData->email;
                    //             $likedUserDataArr[$k]['avatar_type'] = $LUData->avatar_type;
                    //             $user_image     =   "/storage/avatars/dummy.png";
                    //             if( $LUData->avatar_type == "gravatar" ) {
                    //                 $user_image     =   "/storage/avatars/dummy.png";
                    //             } else if ( $LUData->avatar_type == "storage" ) {
                    //                 if( !empty( $LUData->avatar_location ) ){
                    //                     $user_image =   "/storage/".$LUData->avatar_location;
                    //                 } else {
                    //                     $user_image =   "/storage/avatars/dummy.png";
                    //                 }

                    //             } else{
                    //                 $social_Account = SocialAccount::where('user_id','=',$LUData->user_id)->where('provider','=',$LUData->avatar_type)->first();
                    //                 if(!empty($social_Account)){
                    //                     $user_image =   $social_Account->avatar;
                    //                 } else {
                    //                     $user_image =   "/storage/avatars/dummy.png";
                    //                 }
                    //             }
                    //             $likedUserDataArr[$k]['avatar_image'] = $user_image;
                    //         }
                    //         $feedbacklikedUsers[$key]->liked_users = $likedUserDataArr;
                    //     }
                    //     $index++;
                    // }
                    // dd($feedbacklikedUsers);

                    // $likeCount = $feedbackData->map(function ($item) {
                    //     dd($item);
                    //     return json_decode($item->user_liked, true);
                    // })->pluck('user_id')->flatten()->count();

                    $spskill_id=array();
                    $Sproskills = Spskill::with('Skill')->where('user_id','=',$userId)->where('status','=',1)->get();
                    if(!empty($Sproskills)) {
                        foreach ($Sproskills as $sskill) {
                           $spskill_id[]=$sskill->skill_id;
                       }
                    }

                    if( empty($alluserData->Spavailability) || count($alluserData->Spavailability) < 0 ) {
                        $alluserData['spavailabilityNew'] = "{\"monday\":{\"close\":1},\"tuesday\":{\"close\":1},\"wednesday\":{\"close\":1},\"thursday\":{\"close\":1},\"friday\":{\"close\":1},\"saturday\":{\"close\":1},\"sunday\":{\"close\":1}}";
                    }
                   // $sessflag = $request->session()->get('flag');
                   // $flag = '';
                   //  if($sessflag) {
                   //      $flag = 'feedback';
                   //      $request->session()->forget('flag');
                   //  }
                    $user = Auth::user();
                    $checkonline=$userData->isOnline();
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

                    $sp_fav=0;

                    if(!empty($user)){
                        $FavSp  = FavSp::where('user_id','=',$user->id)->where('fav_user_id','=',$userId)->first();

                        if(!empty($FavSp)){
                            $sp_fav=1;
                        }
                    }

                    $advtisement = Ads::where('pagename','userdetailspage')->where('status',1)->get()->toArray();
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

                    $model      = new Feedback;
                    $FavSp      = FavSp::where('fav_user_id', $alluserData->id)->get();

                    $UserAdsDataArr = array();
                    $user_ads = UserAds::with(['getCurrency', 'getAdsSkill'])->where('user_ads.user_id', $alluserData->id)->where('user_ads.status', 1)->get();

                    $skills = Spskill::with(['Skill','currency'])->where('user_id', $alluserData->id)->get();

                    $sitesettingsData = Sitesettings::get();
                    $sitesettingsData = reset($sitesettingsData);


                    $getCurrentMonthVisitors = ProfileVisitors::query()
                    ->whereMonth('created_at', '=', date('m'))
                    ->whereYear('created_at', '=', date('Y'))
                    ->where('visited_user_id', $uid)
                    ->count();

                    $currentYearWeek = date('oW');
                    $getCurrentWeekVisitors = ProfileVisitors::query()
                    ->whereRaw("YEARWEEK(created_at) = $currentYearWeek")
                    ->where('visited_user_id', $uid)
                    ->count();

                    $FavTotal      = FavSp::where('fav_user_id', $uid)->count();

                    $visitorArr = [
                        'current_month' => $getCurrentMonthVisitors,
                        'current_week' => $getCurrentWeekVisitors,
                        'totalFav' => $FavTotal
                    ];
                    return response()->json(
                    [
                        'errors' => '',
                        "status" => 200,
                        "message" => "Success",
                        "isOnlineUser" => $isonline,
                        "current_user_skills" => $Sproskills,
                        "alluserData" => $alluserData,
                        "feedback_data" => $feedbackData,
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
        } else {
            return response()->json(['errors' => 'User is not logged in'],401);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function userDetaislBySlug(Request $request)
    {
        if( auth('sanctum')->check() ) {
            $validation = Validator::make($request->all(), [
                'user_slug'   => 'required|exists:users,slug',
            ]);
            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()]);
            }

            $user_slug = $request->user_slug;
            $userData = User::where('slug',$user_slug)->first();

            if( !empty($userData) ) {
                $userId=$userData->id;
                $spavtar = array();
                // $alluserData = array();
                $userStatus=$userData->active;
                if( $userStatus ) {
                    $alluserData = User::with(['Profile','SocialAccount','Spavailability','Photogallary'])->where('id', '=', $userId)->first();

                    $feedbackData = DB::table('rating')->leftjoin('users', 'users.id', '=', 'rating.from_userid')->leftJoin('social_accounts', function($join){
                            $join->on('social_accounts.user_id', '=', 'rating.from_userid')
                            ->on('users.avatar_type', '=', 'social_accounts.provider');
                        })->select('rating.*','users.first_name','users.last_name','users.avatar_type','users.avatar_location','users.email','social_accounts.provider','social_accounts.avatar')->where('rating.to_userid', $userId)->orderBy('rating.id', 'DESC')->get();

                    $spskill_id=array();
                    $Sproskills = Spskill::with('Skill')->where('user_id','=',$userId)->where('status','=',1)->get();
                    if(!empty($Sproskills)) {
                        foreach ($Sproskills as $sskill) {
                           $spskill_id[]=$sskill->skill_id;
                       }
                    }

                    if( empty($alluserData->Spavailability) || count($alluserData->Spavailability) < 0 ) {
                        $alluserData['spavailabilityNew'] = "{\"monday\":{\"close\":1},\"tuesday\":{\"close\":1},\"wednesday\":{\"close\":1},\"thursday\":{\"close\":1},\"friday\":{\"close\":1},\"saturday\":{\"close\":1},\"sunday\":{\"close\":1}}";
                    }
                   // $sessflag = $request->session()->get('flag');
                   // $flag = '';
                   //  if($sessflag) {
                   //      $flag = 'feedback';
                   //      $request->session()->forget('flag');
                   //  }
                    $user = Auth::user();
                    $checkonline=$userData->isOnline();
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

                    $sp_fav=0;

                    if(!empty($user)){
                        $FavSp  = FavSp::where('user_id','=',$user->id)->where('fav_user_id','=',$userId)->first();

                        if(!empty($FavSp)){
                            $sp_fav=1;
                        }
                    }

                    $advtisement = Ads::where('pagename','userdetailspage')->where('status',1)->get()->toArray();
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

                    $model      = new Feedback;
                    $FavSp      = FavSp::where('fav_user_id', $alluserData->id)->get();

                    $UserAdsDataArr = array();
                    $user_ads = UserAds::with(['getCurrency', 'getAdsSkill'])->where('user_ads.user_id', $alluserData->id)->where('user_ads.status', 1)->get();

                    $skills = Spskill::with(['Skill','currency'])->where('user_id', $alluserData->id)->get();

                    $sitesettingsData = Sitesettings::get();
                    $sitesettingsData = reset($sitesettingsData);

                    return response()->json(
                    [
                        'errors' => '',
                        "status" => 200,
                        "message" => "Success",
                        "isOnlineUser" => $isonline,
                        "current_user_skills" => $Sproskills,
                        "alluserData" => $alluserData,
                        "feedback_data" => $feedbackData,
                        "userAverageRating" => $userAverageRating,
                        "UserAds" => $user_ads,
                        "skills" => $skills,
                        "sitesettingsData" => $sitesettingsData,
                        "null_spavailability" => "{\"monday\":{\"from\":null,\"to\":null,\"close\":\"1\"},\"tuesday\":{\"from\":null,\"to\":null,\"close\":\"1\"},\"wednesday\":{\"from\":null,\"to\":null,\"close\":\"1\"},\"thursday\":{\"from\":null,\"to\":null,\"close\":\"1\"},\"friday\":{\"from\":null,\"to\":null,\"close\":\"1\"},\"saturday\":{\"from\":null,\"to\":null,\"close\":\"1\"},\"sunday\":{\"from\":null,\"to\":null,\"close\":\"1\"}}"
                    ],200);
                } else {
                    return response()->json(['errors' => 'No data available']);
                }
            } else {
                return response()->json(['errors' => 'No data available']);
            }

        }  else {
            return response()->json(['errors' => 'User is not logged in'],401);
        }
    }

    /**
     * Show the all notifications of auth user.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUserNotifications(Request $request)
    {
        //
        if( auth('sanctum')->check() ) {
            $validation = Validator::make($request->all(), [
                'uid'   => 'required|exists:users,id,deleted_at,NULL',
            ]);

            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()]);
            }

            $userId = $request->uid;
            $pageSize = $request->per_page;

            $getAllNotifications = UserNotifications::with('senderUserData', 'receiverUserData')->orderBy('id', 'desc')->paginate($pageSize);
            return response()->json(
            [
                'errors' => '',
                "status" => 200,
                "message" => "Success",
                "results" => $getAllNotifications,
            ],200);
        } else {
            return response()->json(['errors' => 'User is not logged in'],401);
        }
    }

    /**
     * Delete user notification using notification id.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteUserNotification(Request $request)
    {
        if( auth('sanctum')->check() ) {
            $validation = Validator::make($request->all(), [
                'notification_id'   => 'required|exists:user_notifications,id',
            ]);

            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()]);
            }

            $deleteUserNotification = UserNotifications::where('id',$request->notification_id)->firstOrFail();

            if($deleteUserNotification->delete()) {
                $message = array('message' => 'Notification deleted successfully!');
                $errors = array();
            } else {
                $message = array();
                $errors = array('message' => 'Notification not deleted!');
            }

            return response()->json(
            [
                'errors' => $errors,
                "status" => 200,
                "message" => $message,
            ],200);
        } else {
            return response()->json(['errors' => 'User is not logged in'],401);
        }
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
    public function destroy(Request $request)
    {
        //
        if( auth('sanctum')->check() ) {
            $validation = Validator::make($request->all(), [
                'uid'   => 'required|exists:users,id,deleted_at,NULL',
            ]);

            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()]);
            }

            $user = User::find($request->uid);
            $user->Profile()->forcedelete();
            $user->Spskill()->delete();
            $user->SocialAccount()->delete();
            $user->Spavailability()->delete();
            $user->Photogallary()->delete();
            $user->fromUserRating()->delete();
            $user->toUserRating()->delete();
            $user->conversationUserOne()->delete();
            $user->conversationUserTwo()->delete();
            $user->mainuserofFavourite()->delete();
            $user->favouriteUser()->delete();

            PasswordHistory::where('user_id', '=', $request->uid)->delete();
            Message::where('user_id', '=', $request->uid)->delete();
            Message::where('receiver_id', '=', $request->uid)->delete();
            OverallProfileRating::where('uid',$request->uid)->delete();

            $delete_account = $user->forcedelete();

            if( $delete_account ) {
                $message = array('message' => __('User deleted successfully!'));
                $errors = array();
            } else {
                $message = array();
                $errors = array('message' => __('Whoops, User not delete.'));
            }

            return response()->json(
            [
                'errors' => $errors,
                "status" => 200,
                "message" => $message,
            ],200);
        } else {
            return response()->json(['errors' => 'User is not logged in'],401);
        }
    }

    public function destroyOldAccounts(Request $request)
    {
        //
        $usersIds = explode(',', $request->user_ids);
        foreach( $usersIds as $k => $userId ) {
            $user = User::withTrashed()->find($userId);
            $user->Profile()->forcedelete();
            $user->Spskill()->delete();
            $user->SocialAccount()->delete();
            $user->Spavailability()->delete();
            $user->Photogallary()->delete();
            $user->fromUserRating()->delete();
            $user->toUserRating()->delete();
            $user->conversationUserOne()->delete();
            $user->conversationUserTwo()->delete();
            $user->mainuserofFavourite()->delete();
            $user->favouriteUser()->delete();

            PasswordHistory::where('user_id', '=', $userId)->delete();
            Message::where('user_id', '=', $userId)->delete();
            Message::where('receiver_id', '=', $userId)->delete();
            OverallProfileRating::where('uid',$userId)->delete();

            $delete_account = $user->forcedelete();

            if( $delete_account ) {
                $message = array('message' => __('User deleted successfully!'));
                $errors = array();
            } else {
                $message = array();
                $errors = array('message' => __('Whoops, User not delete.'));
            }

        }
        return response()->json(
        [
            'errors' => $errors,
            "status" => 200,
            "message" => $message,
        ],200);
    }


    /**
     * Logout user from app
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        //
        $islogout = $request->user()->currentAccessToken()->delete();

        if( $islogout ){
            $message = array('message' => __('User logout successfully!'));
            $errors = array();
        } else {
            $message = array();
            $errors = array('message' => __('Whoops, looks like something went wrong'));
        }

        return response()->json(
        [
            'errors' => $errors,
            "status" => 200,
            "message" => $message,
        ],200);
    }
}
