<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Models\Profile;
use App\Models\FavSp;
use App\Models\Auth\User;
use App\Models\Feedback;
use App\Models\Spskill;
use App\Models\Skill;
use App\Models\Currency;
use App\Models\ReportAccount;
use App\Models\UserBadge;
use App\Models\UserNotifications;
use App\Repositories\Backend\Auth\UserRepository;

// use Kreait\Firebase\Factory;
// use \Kreait\Firebase\Contract\Messaging;
// use \Kreait\Firebase\Messaging\CloudMessage;
// use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
// use Kreait\Firebase\Messaging\AndroidConfig;

class ProfileApiController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * ProfileApiController constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
    * Assign or choose user badge from User profile API
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function updateUserBadges(Request $request)
    {
        if(auth('sanctum')->check()) {
            $validator = Validator::make($request->all(), [
                'uid' => 'required|exists:users,id,deleted_at,NULL',
                'ub_id' => 'required|exists:user_badges,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $userProfile = Profile::where('user_id', $request->uid)->first();

            if( !empty( $userProfile ) ) {
                if( $request->type == 'add' ) {
                    $userProfile->badge_id = $request->ub_id;
                } elseif( $request->type == 'remove' ) {
                    $userProfile->badge_id = NULL;
                }
                $updateUserProfile = $userProfile->save();
            } else {
                $updateUserProfile =  Profile::create([
                    'user_id' => $request->uid,
                    'badge_id' => $request->ub_id,
                ]);
            }

            if( $updateUserProfile ) {
                $message = "User badge updated successfully.";
                $errors = "";
                $status = 200;
            } else {
                $message = "";
                $errors = "User badge does not update.";
                $status = 200;
            }

            return response()->json(
            [
                'errors' => $errors,
                "status" => $status,
                "message" => $message,
            ],200);
        } else {
            return response()->json(['errors' => 'User is not logged in'],401);
        }
    }

     /**
    * Update about me
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function updateaboutMe(Request $request)
    {
        if(auth('sanctum')->check()) {
            $validator = Validator::make($request->all(), [
                'uid' => 'required|exists:users,id,deleted_at,NULL',
                'about' => 'nullable',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $about = $request->about;

            $userProfile = Profile::where('user_id', $request->uid)->first();

            if( !empty( $userProfile ) ) {
                $userProfile->about = $about;
                $updateAbout = $userProfile->save();
            } else {
                $updateAbout =  Profile::create([
                    'user_id' => $request->uid,
                    'about' => $about,
                ]);
            }

            if( $updateAbout ) {
                $message = "About updated successfully.";
                $errors = "";
                $status = 200;
            } else {
                $message = "";
                $errors = "About does not update.";
                $status = 200;
            }

            return response()->json(
            [
                'errors' => $errors,
                "status" => $status,
                "message" => $message,
            ],200);
        } else {
            return response()->json(['errors' => 'User is not logged in'],401);
        }
    }


    public function uploadAvatar(Request $request) {
        $image = $request->file('avtar_image');
        $avatarImage = null;
        if ($image) {
            $imageType = $image->getClientOriginalExtension();
            $imageData = file_get_contents($image);
            $base64Image = base64_encode($imageData);
            $avatarImage = 'data:image/' . $imageType . ';base64,' . $base64Image;
        }
        // // dd($avatarImage);
        $result = $this->userRepository->uploadAvatar(
            $request->user()->id,
            $request->only('avatar_type', 'avatar_location'),
            $request->has('avatar_location') ? $request->file('avatar_location') : false,
            !empty($avatarImage) ? $avatarImage :false
        );

        if(empty($result['errors'])) {
            $message = "Profile Avatar updated successfully.";
            $errors = null;
            $location = $result['success']['location'];
            $status = 200;
        } else {
            $message = null;
            $errors = "Profile avatar not updated successfully.";
            $location = null;
            $status = 200;
        }

        return response()->json(
        [
            'errors' => $errors,
            "status" => $status,
            "message" => $message,
            "location" => $location,
        ],200);
    }

    /**
    * Get list of fav persons
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function getFavUsers(Request $request){
        $validation = Validator::make($request->all(), [
            'uid'   => 'required|exists:users,id,deleted_at,NULL',
        ]);

        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()]);
        }

        $loged = auth()->user();
        DB::enableQueryLog();
        $datas = DB::table('fav_sp')
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
            'sp_skill.id AS SpId',
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
        ->leftjoin('profile', 'profile.user_id', '=', 'fav_sp.fav_user_id')
        ->leftjoin('users', 'users.id', '=', 'fav_sp.fav_user_id')
        ->leftjoin('sp_skill', 'sp_skill.user_id', '=', 'fav_sp.fav_user_id')
        ->leftjoin('skill', 'skill.id', '=', 'fav_sp.fav_user_id')
        ->leftjoin('currency', 'currency.id', '=', 'sp_skill.currency_id')
        ->where('fav_sp.user_id', '=',$loged->id)
        ->orderBy('users.id', 'DESC')
        ->get();
        // dd(DB::getQueryLog());
        $result = array();
        if(!empty($datas)){
            $i=0;
            $existsUserId = 0;
            foreach ($datas as $key => $value) {
                if( $existsUserId !=  $value->user_id) {
                    $existsUserId = $value->user_id;
                    $userAverageRating = Feedback::user_average_rating($value->user_id);
                   $userAverageRating = round($userAverageRating);
                   $user = User::find($value->user_id);
                   // $isOnline = $user->isOnline();
                   $isOnline = 0;
                   $last_login = $user->updated_at->diffForHumans();
                   //$diffForHumans = User::find($value->user_id)->diffForHumans();
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
                    $result[$i]['sp_slug']=$value->slug;
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

                    $result[$i]['sp_skill_description']=$value->sp_skill_description;
                    $result[$i]['sp_skill_price_per_hour']=$value->sp_skill_price_per_hour;
                    $result[$i]['sp_skill_price_per_day']=$value->sp_skill_price_per_day;
                    $result[$i]['sp_skill_show_price']=__("strings.new.".$value->sp_skill_show_price);
                    $result[$i]['sp_skill_offer_discount']=$value->sp_skill_offer_discount;
                    $result[$i]['sp_skill_offer_desc']=$value->sp_skill_offer_desc;
                    $result[$i]['sp_skill_offer_img']="/storage/spskills/".$value->sp_skill_offer_img;
                    $result[$i]['sp_skill_offer_start_date']=$value->sp_skill_offer_start_date;
                    $result[$i]['sp_skill_offer_end_date']=$value->sp_skill_offer_end_date;

                    $result[$i]['sp_skill_si_offer']=0;

                    $i++;
                }
            }
        }

        return response()->json(
        [
            'errors' => '',
            "status" => 200,
            "message" => "Success",
            "results" => $result,
        ],200);
    }

    /**
    * Add to fav person
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function AddToFav(Request $request)
    {
        // $messaging = app('firebase.messaging');
        $validation = Validator::make($request->all(), [
            'uid'   => 'required|exists:users,id,deleted_at,NULL',
            'fav_uid' => 'required|exists:users,id,deleted_at,NULL',
        ]);

        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()]);
        }
        $data = array();
        $GetFavSp  = FavSp::where('user_id','=',$request->uid)->where('fav_user_id','=',$request->fav_uid)->first();

        $user = User::where('id',$request->fav_uid)->firstOrFail();

        if( !empty($GetFavSp) ) {
            $GetFavSp->delete();
            $data['status']=1;
            $data['add_fav']=0;
            $data['remove_fav']=1;
            $data['message']= "User removed from favourite";
        } else {

            $FavSp = new FavSp;
            $FavSp->user_id = $request->uid;
            $FavSp->fav_user_id = $request->fav_uid;
            $FavSp->save();
            $data['status']=1;
            $data['add_fav']=1;
            $data['remove_fav']=0;
            $data['message']="Add to favourite";

            try {
                $receiverDeviceToken = $user->device_token;
                $authUser = auth()->user();
                // if( !empty($receiverDeviceToken) ) {
                //     $deviceToken = $receiverDeviceToken;

                //     $messageApp = CloudMessage::fromArray([
                //         'token' => $deviceToken,
                //         'notification' => [
                //             "title" => "Added to favourite",
                //             "body" => $authUser->name." added you to favourite.",
                //             "sound"=> 'default',
                //         ], // optional
                //         "apns" => [
                //             "headers" => [
                //                 'apns-priority' => '10',
                //             ],
                //             "payload" => [
                //                 "aps" => [
                //                     "sound" => 'notification_sound.aac',
                //                 ],
                //             ],
                //         ],
                //         'android' => [
                //             'notification' => [
                //                 'channel_id' => 'helpii_notifications_channel',
                //                 "sound" => "notification_sound.mp3",
                //             ],
                //         ],
                //         'data' => [
                //             "title" => "Added to favourite",
                //             "body" => $authUser->name." added you to favourite.",
                //             "type" => 'added_to_fav',
                //             "sender_id" => auth()->id()
                //         ],
                //     ]);
                //     // dd($messageApp);
                //     $messaging->send($messageApp);

                // }

                $userNotificationData = [
                    'sender_user_id' => auth()->id(),
                    'receiver_user_id' => $request->fav_uid,
                    'notification_message' => $authUser->name." added you to favourite.",
                    'notification_type' => 3,
                ];

                UserNotifications::create($userNotificationData);
            } catch (\Exception $e) {
                // $data['exception_error'] = $e->getMessage();
                return response()->json(['errors' => $e->getMessage(), 'status' => 200, 'message' => 'Add to favourite'], 200);
            }
        }

        $FavSp = FavSp::where('fav_user_id', $request->fav_uid)->get();
        $FavSpCount = FavSp::where('user_id', $request->uid)->count();

        $data['total_fav'] = count($FavSp);

        return response()->json(
        [
            'errors' => '',
            "status" => 200,
            "message" => "Success",
            "results" => $data,
        ],200);
    }
}
