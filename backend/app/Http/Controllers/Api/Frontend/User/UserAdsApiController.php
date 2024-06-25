<?php

namespace App\Http\Controllers\Api\Frontend\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\UserAds;
use App\Models\Spskill;
use App\Models\Skill;
use App\Models\Auth\User;
use App\Models\UserNotifications;
use Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Kreait\Firebase\Factory;
use \Kreait\Firebase\Contract\Messaging;
use \Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Kreait\Firebase\Messaging\AndroidConfig;

class UserAdsApiController extends Controller
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
        //
        $messaging = app('firebase.messaging');
        if( auth('sanctum')->check() ) {

            $user = Auth::user();
            $validator = Validator::make($request->all(), [
                'skill' => 'required',
                'title' => 'required|max:300',
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
                'address' => 'required',
                'pincode' => 'nullable',
                'latitude' => 'required',
                'longitudes' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $UserAds                    = new UserAds();
            $UserAds->user_id           = $request->uid;
            $UserAds->skill_id          = $request->skill;
            $UserAds->title             = $request->title;
            $UserAds->description       = $request->description;
            $UserAds->price_per_hour    = $request->price_per_hour != '' ? $request->price_per_hour : 0;
            $UserAds->price_per_day     = $request->price_per_day != '' ? $request->price_per_day : 0;
            $UserAds->show_price        = $request->show_price;
            $UserAds->address           = $request->address;
            $UserAds->city              = $request->city;
            $UserAds->state             = $request->state;
            $UserAds->country           = $request->country;
            $UserAds->pincode           = $request->pincode;
            $UserAds->latitude          = $request->latitude;
            $UserAds->longitudes        = $request->longitudes;
            $UserAds->currency_id       = 2;
            $UserAds->status            = $request->status;

            // dd($UserAds);
            if($UserAds->save()) {
                $message = __('strings.new.user_ads_create_message');
                $errors = "";
                $spSkillsUsers = collect();

                $skillDetails = Skill::where('id', $request->skill)->first();
                $getUsersFromSkils = Spskill::where('skill_id', $request->skill)->where('city', $request->city)->get();
                foreach ($getUsersFromSkils as $key => $spSkillUsers) {
                    $userDetails = User::where('id', $spSkillUsers->user_id)->first();
                    $existsUser = 0;
                    if( !$spSkillsUsers->contains('id', $userDetails->id) ) {
                        $getAuthUser = auth()->user();
                        $getDeviceToken = $userDetails->device_token;
                        if( !empty( $getDeviceToken ) ) {

                            // $messageApp = CloudMessage::fromArray([
                            //     'token' => $getDeviceToken,
                            //     'notification' => [
                            //         "title" => "New Feedback",
                            //         "body" => $getAuthUser->name." has recently create ".$request->title." Add for Skill : ".$skillDetails->name." in your city ".$request->city.". Please check.",
                            //     ], // optional
                            //     'data' => [
                            //         "type" => 'new_feedback',
                            //         "sender_id" => auth()->id()
                            //     ],
                            // ]);
                            $messageApp = CloudMessage::fromArray([
                                'token' => $getDeviceToken,
                                'notification' => [
                                    "title" => "New Ads Created",
                                    "body" => $getAuthUser->name." has recently create ".$request->title." Add for Skill : ".$skillDetails->name." in your city ".$request->city.". Please check.",
                                    "sound"=> 'default',
                                ], // optional
                                "apns" => [
                                    "headers" => [
                                        'apns-priority' => '10',
                                    ],
                                    "payload" => [
                                        "aps" => [
                                            "sound" => 'notification_sound.aac',
                                        ],
                                    ],
                                ],
                                'android' => [
                                    'notification' => [
                                        'channel_id' => 'helpii_notifications_channel',
                                        "sound" => "notification_sound.mp3",
                                    ],
                                ],
                                'data' => [
                                    "title" => "New Ads Created",
                                    "body" => $getAuthUser->name." has recently create ".$request->title." Add for Skill : ".$skillDetails->name." in your city ".$request->city.". Please check.",
                                    "type" => 'new_created_ads',
                                    "sender_id" => auth()->id()
                                ],
                            ]);
                            // dd($messageApp);
                            // try {
                                $messaging->send($messageApp);
                            // } catch(\Exception $e) {
                            //     return response()->json(['errors' => $e->getMessage()],200);
                            // }
                            $spSkillsUsers->add($userDetails); //ignore it just for check user for skill
                        }
                        $userNotificationData = [
                            'sender_user_id' => $request->uid,
                            'receiver_user_id' => $userDetails->id,
                            'notification_message' => $getAuthUser->name." has recently create ".$request->title." Add for Skill : ".$skillDetails->name." in your city ".$request->city.". Please check.",
                            'notification_type' => 6,
                        ];
                        UserNotifications::create($userNotificationData);
                    }
                }
            } else {
                $message = "";
                $errors = __('strings.new.user_ads_create_failed');
            }
            return response()->json(
            [
                'errors' => '',
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
    public function edit(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'uid' => 'required|exists:user_ads,user_id',
            'ads_id' => 'required|exists:user_ads,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $getEditUser = UserAds::where('id', $request->ads_id)->where('user_id',$request->uid)->firstOrFail();
        if($getEditUser) {
            $message = 'Ads found successfully!';
            $errors = '';
            $adsdata = $getEditUser;
        } else {
            $message = '';
            $errors = 'Ads not found!';
            $adsdata = '';
        }
        return response()->json(
        [
            'errors' => $errors,
            "status" => 200,
            "message" => $message,
            "adsdata" => $adsdata
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
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'uid' => 'required|exists:user_ads,user_id',
            'ads_id' => 'required|exists:user_ads,id',
            'skill' => 'required',
            'title' => 'required|max:300',
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
            'address' => 'required',
            'pincode' => 'required',
            'latitude' => 'required',
            'longitudes' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $updated = UserAds::where('id', $request->ads_id)->where('user_id',$request->uid)->update(
        [
            'skill_id' => $request->skill,
            'currency_id' => 2,
            'title' => $request->title,
            'description' => $request->description,
            'price_per_hour' => $request->price_per_hour != '' ? $request->price_per_hour : 0,
            'price_per_day' => $request->price_per_day != '' ? $request->price_per_day : 0,
            'show_price' => $request->show_price,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'pincode' => $request->pincode,
            'latitude' => $request->latitude,
            'longitudes' => $request->longitudes,
            'status' => $request->status,
        ]);

        if($updated) {
            $message = array('message' => __('strings.new.user_ads_update_message'));
            $errors = array();
        } else {
            $message = array();
            $errors = array('message' => __('strings.new.user_ads_create_failed'));
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
            'uid' => 'required|exists:user_ads,user_id',
            'ads_id' => 'required|exists:user_ads,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        $UserAds = UserAds::where('id',$request->ads_id)->where('user_id',$request->uid)->firstOrFail();

        if($UserAds->delete()) {
            $message = array('message' => 'Ads deleted successfully!');
            $errors = array();
        } else {
            $message = array();
            $errors = array('message' => 'Add not deleted!');
        }

        return response()->json(
        [
            'errors' => $errors,
            "status" => 200,
            "message" => $message,
        ],200);
    }
}
