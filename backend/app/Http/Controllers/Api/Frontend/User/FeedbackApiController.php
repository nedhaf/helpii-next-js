<?php

namespace App\Http\Controllers\Api\Frontend\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use App\Models\Feedback;
use App\Models\OverallProfileRating;
use App\Models\Auth\User;
use Illuminate\Support\Facades\DB;
use App\Models\Spskill;
use App\Models\Skill;
use App\Models\UserNotifications;

use Illuminate\Support\Facades\Mail;
use Kreait\Firebase\Factory;
use \Kreait\Firebase\Contract\Messaging;
use \Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Kreait\Firebase\Messaging\AndroidConfig;

class FeedbackApiController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:sanctum');
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
        // $messaging = app('firebase.messaging');
        if(auth('sanctum')->check()) {

            $validator = Validator::make($request->all(), [
                'from_userid' => 'required|exists:users,id',
                'to_userid' => 'required|exists:users,id',
                'sp_skill_id' => 'required|exists:skill,id',
                'review' => 'required',
                'value_for_money' => 'required|numeric|min:1|max:5',
                'quality_of_work' => 'required|numeric|min:1|max:5',
                'relation_with_customer' => 'required|numeric|min:1|max:5',
                'performance' => 'required|numeric|min:1|max:5',
            ], [
              'sp_skill_id.required' => 'Please select skill.',
              'sp_skill_id.exists' => 'Please select valid skill.',
              'review.required' => 'Please write some reviews.'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $flag = 'feedback';
            $feedback = new Feedback();
            $feedback->from_userid = $request->from_userid;
            $feedback->to_userid = $request->to_userid;
            $feedback->value_for_money = $request->value_for_money;
            $feedback->quality_of_work = $request->quality_of_work;
            $feedback->relation_with_customer = $request->relation_with_customer;
            $feedback->performance = $request->performance;
            $feedback->review = $request->review;
            $feedback->sp_skill_id = $request->sp_skill_id;

            $total = (((int) $request->value_for_money + (int) $request->quality_of_work + (int) $request->relation_with_customer + (int) $request->performance) / 4);
            $feedback->total = (float) $total;

            // Overall Ratind


            // send mail to service provider...
            $user = User::where('id',$request->to_userid)->firstOrFail();
            $FromUserData = User::where('id',$request->from_userid)->firstOrFail();
            $skill = Skill::getSkillNameById($request->sp_skill_id);
            $data = [];

            if( $feedback->save() ) {
                $totalAvg = Feedback::user_average_rating($request->to_userid);

                $getOverallFeedback = OverallProfileRating::where('uid', $request->to_userid)->get();

                $userAverageRating = round($totalAvg);
                if( count($getOverallFeedback) > 0 ) {
                    $updateOverallRating = OverallProfileRating::where('uid', $request->to_userid)->update(['total_rating' => $userAverageRating]);
                } else {
                    $overallRating = new OverallProfileRating();
                    $overallRating->uid = $request->to_userid;
                    $overallRating->total_rating = $userAverageRating;
                    $overallRating->save();
                }

                $data['html'] = '
                    <p><strong>Hello '.$user->first_name.' '.$user->last_name.',</strong></p>
                    <p><strong>'.$FromUserData->first_name.' '.$FromUserData->last_name.'</strong> has been submitted feedback for your skill.<p>
                    <p>&nbsp;</p>
                    <p><strong>Skill:</strong> '.$skill.'</p>
                    <p><strong>Rating:</strong></p>
                    <table width="100%">
                        <tr><td>Value for Money:</td><td>'.$request->value_for_money.' out of 5 stars</td></tr>
                        <tr><td>Relation with Customer:</td><td>'.$request->relation_with_customer.' out of 5 stars</td></tr>
                        <tr><td>Quality of Work:</td><td>'.$request->quality_of_work.' out of 5 stars</td></tr>
                        <tr><td>Performance:</td><td>'.$request->performance.' out of 5 stars</td></tr>
                        <tr><td><strong>Total:</strong></td><td><strong>'.round($total).' out of 5 stars</strong></td></tr>
                    </table>
                    <p><strong>Review:</strong> '.$request->review.'</p>
                ';
                // This is Send Email Func. Must Uncomment
                // Mail::send('frontend.mail.offlineMessage', $data, function($mail_msg) use($user, $data) {
                //     $mail_msg->subject("Helpii - New feedback arrived!");
                //     $mail_msg->from(config('mail.from.address'), config('mail.from.name'));
                //     $mail_msg->to($user->email, $user->first_name." ".$user->last_name);
                // });

                try {
                    $receiverDeviceToken = $user->device_token;
                    $getDeviceToken = auth()->user();
                    // Mobile Notification code :- Need to uncommented on live
                    // if( !empty($receiverDeviceToken) ) {
                    //     $deviceToken = $receiverDeviceToken;

                    //     // $messageApp = CloudMessage::fromArray([
                    //     //     'token' => $deviceToken,
                    //     //     'notification' => [
                    //     //         "title" => "New Feedback",
                    //     //         "body" => $getDeviceToken->name." rated your profile",
                    //     //     ], // optional
                    //     //     'data' => [
                    //     //         "type" => 'new_feedback',
                    //     //         "sender_id" => auth()->id()
                    //     //     ],
                    //     // ]);
                    //     $messageApp = CloudMessage::fromArray([
                    //         'token' => $deviceToken,
                    //         'notification' => [
                    //             "title" => "New Feedback",
                    //             "body" => $getDeviceToken->name." rated your profile",
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
                    //             "title" => "New Feedback",
                    //             "body" => $getDeviceToken->name." rated your profile",
                    //             "type" => 'new_feedback',
                    //             "sender_id" => auth()->id()
                    //         ],
                    //     ]);
                    //     // dd($messageApp);
                    //     $messaging->send($messageApp);
                    // }

                    $userNotificationData = [
                        'sender_user_id' => $request->from_userid,
                        'receiver_user_id' => $request->to_userid,
                        'notification_message' => $getDeviceToken->name." rated your profile",
                        'notification_type' => 4,
                    ];

                    UserNotifications::create($userNotificationData);
                } catch (\Exception $e) {
                    return response()->json(['errors' => $e->getMessage(), 'status' => 200, 'message' => __('Rating saved successfully.')], 200);
                }

                $message = __('Rating saved successfully.');
                $errors = '';
            } else {
                $message = '';
                $errors = __('Opps! Something went wrong.');
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

    // Test notification
    public function testNotification(Request $request)
    {
        $messaging = app('firebase.messaging');
      
            if(auth('sanctum')->check()) {
                // $user = User::where('id',$request/)->firstOrFail();
                $receiverDeviceToken = $request->device_token;
                
                if( !empty($receiverDeviceToken) ) {
                    $getDeviceToken = auth()->user();
                    $deviceToken = $receiverDeviceToken;
                    

                    $messageApp = CloudMessage::fromArray([
                        'token' => $deviceToken,
                        'notification' => [
                            "title" => "New Message",
                            "body" => " has sent you a message.",
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
                            "title" => "New Message",
                            "body" => "has sent you a message.",
                            "type" => 'new_message',
                            "sender_id" => auth()->id()
                        ],
                    ]);
                    $messaging->send($messageApp);
                }
            } else {
                return response()->json(['errors' => 'User is not logged in'],401);
            }
       
    }

    /**
     * Get the All feedbacks of specific user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getUserFeedbacks(Request $request)
    {
        if(auth('sanctum')->check()) {
            $userId = auth()->id();
            try {
                $getFeedbacks = Feedback::with('Skill')->leftjoin('users', 'users.id', '=', 'rating.to_userid')
                ->leftJoin('social_accounts', function($join){
                    $join->on('social_accounts.user_id', '=', 'rating.to_userid');
                })
                ->select('rating.*','users.first_name','users.last_name','users.avatar_type','users.avatar_location','users.email','social_accounts.provider','social_accounts.avatar')
                ->where('rating.from_userid', $userId)
                ->orderBy('rating.id', 'desc')
                ->get();

                return response()->json([
                    "status" => 200,
                    "message" => "Success",
                    "results" => $getFeedbacks
                ],200);
            } catch (\Exception $e) {
                // Log the error or handle it in a way that suits your application
                return response()->json(['errors' => $e->getMessage()],200);
            }
        } else {
            return response()->json(['errors' => 'User is not logged in'],401);
        }
    }

    /**
     * Get the All feedbacks with total
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getFeedbacks(Request $request)
    {
        try {
            $getFeedbacks = Feedback::get();

            foreach( $getFeedbacks as $k => $getFeedback ) {
                $toId = $getFeedback->to_userid;
                $totalRating = $getFeedback->total;

                $overall_rating = new OverallProfileRating();
                $overall_rating->user_id = $toId;
                $overall_rating->total_rating = $totalRating;
                $overall_rating->save();
            }
        } catch (\Exception $e) {
            // Log the error or handle it in a way that suits your application
            return response()->json(['errors' => $e->getMessage()],200);
        }
        return response()->json([
            'errors' => '',
            'message' => 'Overall ratings created successfully.'
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
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id (optional)
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
        if(auth('sanctum')->check()) {
            $validator = Validator::make($request->all(), [
                'feedback_id' => 'required|exists:rating,id',
                'sp_skill_id' => 'required|exists:skill,id',
                'review' => 'required',
                'value_for_money' => 'required|numeric|min:1|max:5',
                'quality_of_work' => 'required|numeric|min:1|max:5',
                'relation_with_customer' => 'required|numeric|min:1|max:5',
                'performance' => 'required|numeric|min:1|max:5',
            ], [
              'sp_skill_id.required' => 'Please select skill.',
              'sp_skill_id.exists' => 'Please select valid skill.',
              'review.required' => 'Please write some reviews.'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $feedbackId = $request->feedback_id;
            $updateFeedback = Feedback::where('id', $feedbackId)->first();

            $total = (((int) $request->value_for_money + (int) $request->quality_of_work + (int) $request->relation_with_customer + (int) $request->performance) / 4);

            $updateFeedback->value_for_money = $request->value_for_money;
            $updateFeedback->quality_of_work = $request->quality_of_work;
            $updateFeedback->relation_with_customer = $request->relation_with_customer;
            $updateFeedback->performance = $request->performance;
            $updateFeedback->review = $request->review;
            $updateFeedback->total = (float) $total;
            $updateFeedback->sp_skill_id = $request->sp_skill_id;
            $updateFeedback->updated_at = date('Y-m-d H:i:s');

            if( $updateFeedback->save() ) {
                $totalAvg = Feedback::user_average_rating($updateFeedback->to_userid);

                $userAverageRating = round($totalAvg);
                $getOverallFeedback = OverallProfileRating::where('uid', $updateFeedback->to_userid)->get();

                $updateOverallRating = OverallProfileRating::where('uid', $updateFeedback->to_userid)->update(
                    [
                        'total_rating' => $userAverageRating,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]
                );

                $message = __('Feedback updated successfully.');
                $errors = null;
                $feedbackData = $updateFeedback;
            } else {
                $message = null;
                $errors = __('Opps! feedback is not updated.');
                $feedbackData = null;
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
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        //
        if(auth('sanctum')->check()) {
            $validator = Validator::make($request->all(), [
                'feedback_id' => 'required|exists:rating,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $feedbackId = $request->feedback_id;
            $deleteFeedback = Feedback::where('id', $feedbackId)->first();

            if($deleteFeedback->delete()) {
                $message = 'Feedback deleted successfully!';
                $errors = '';
            } else {
                $message = '';
                $errors = 'Feedback not deleted!';
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
     * Like and dislike feedback.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function likedislikeFeedback(Request $request)
    {
        if(auth('sanctum')->check()) {
            $validator = Validator::make($request->all(), [
                'feedback_id' => 'required|exists:rating,id',
                'user_id' => 'required|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $feedbackId = $request->feedback_id;
            $userId = $request->user_id;
            $likeType = $request->like_type;

            $checkFeedback = Feedback::where('id', $feedbackId)->first();
            $getLikedFeedback = $checkFeedback->user_liked;
            $getDisLikedFeedback = $checkFeedback->user_disliked;
            $getToUserFeedback = $checkFeedback->to_userid;
            $getToUser = User::where('id', $getToUserFeedback)->first();
            $user = User::where('id', $userId)->first();

            switch ($likeType) {
                case 'liked':
                    if( !empty( $getDisLikedFeedback ) ) {
                        $getDisLikedFeedback = json_decode($getDisLikedFeedback, true);
                        if( in_array($userId, $getDisLikedFeedback['user_id']) ) {
                            $userIdKey = array_search($userId, $getDisLikedFeedback['user_id']);
                            unset($getDisLikedFeedback['user_id'][$userIdKey]);
                            $getDisLikedFeedback['user_id'] = array_values($getDisLikedFeedback['user_id']);
                            $updatedDisLikedFeedback = json_encode($getDisLikedFeedback);
                            $checkFeedback->user_disliked = $updatedDisLikedFeedback;
                            $checkFeedback->save();
                        }
                    }

                    if( !empty( $getLikedFeedback ) ) {
                        $getLikedFeedback = json_decode($getLikedFeedback, true);
                        if( !in_array($userId, $getLikedFeedback['user_id']) ) {
                            // Add $userId to the array
                            $getLikedFeedback['user_id'][] = $userId;

                            // Convert the array back to JSON
                            $updatedLikedFeedback = json_encode($getLikedFeedback);
                            $checkFeedback->user_liked = $updatedLikedFeedback;

                            if( $checkFeedback->save() ) {
                                $message = "Liked successfully.";
                                $errors = "";
                                $userNotificationData = [
                                    'sender_user_id' => $userId,
                                    'receiver_user_id' => $getToUserFeedback,
                                    'notification_message' => $user->name." has liked your feedback.",
                                    'notification_type' => 1,
                                ];
                                UserNotifications::create($userNotificationData);
                            } else {
                                $message = "";
                                $errors = "Somethig went wrong!";
                            }
                            return response()->json(
                            [
                                'errors' => $errors,
                                "status" => 200,
                                "message" => $message,
                            ],200);
                        } else {
                            if( !empty( $getLikedFeedback ) ) {
                                if( in_array($userId, $getLikedFeedback['user_id']) ) {
                                    $userIdKey = array_search($userId, $getLikedFeedback['user_id']);
                                    unset($getLikedFeedback['user_id'][$userIdKey]);
                                    $getLikedFeedback['user_id'] = array_values($getLikedFeedback['user_id']);
                                    $updatedLikedFeedback = json_encode($getLikedFeedback);
                                    $checkFeedback->user_liked = $updatedLikedFeedback;

                                    if( $checkFeedback->save() ) {
                                        $message = "Disliked successfully.";
                                        $errors = "";
                                        $userNotificationData = [
                                            'sender_user_id' => $userId,
                                            'receiver_user_id' => $getToUserFeedback,
                                            'notification_message' => $user->name." has disliked your feedback.",
                                            'notification_type' => 2,
                                        ];
                                        UserNotifications::create($userNotificationData);
                                    } else {
                                        $message = "";
                                        $errors = "Somethig went wrong!";
                                    }
                                    return response()->json(
                                    [
                                        'errors' => $errors,
                                        "status" => 200,
                                        "message" => $message,
                                    ],200);
                                }
                            } else {
                                return response()->json(
                                [
                                    'errors' => '',
                                    "status" => 200,
                                    "message" => 'liked not removed.'
                                ],200);
                            }
                        }
                    } else {
                        $userLiked = ['user_id' => array($userId)];
                        $checkFeedback->user_liked = json_encode($userLiked);

                        if( $checkFeedback->save() ) {
                            $message = "Liked successfully.";
                            $errors = "";
                            $userNotificationData = [
                                'sender_user_id' => $userId,
                                'receiver_user_id' => $getToUserFeedback,
                                'notification_message' => $user->name." has liked your feedback.",
                                'notification_type' => 1,
                            ];
                            UserNotifications::create($userNotificationData);
                        } else {
                            $message = "";
                            $errors = "Somethig went wrong!";
                        }
                        return response()->json(
                        [
                            'errors' => $errors,
                            "status" => 200,
                            "message" => $message,
                        ],200);
                    }
                    break;
                case 'disliked':
                    if( !empty( $getLikedFeedback ) ) {
                        $getLikedFeedback = json_decode($getLikedFeedback, true);
                        if( in_array($userId, $getLikedFeedback['user_id']) ) {
                            $userIdKey = array_search($userId, $getLikedFeedback['user_id']);
                            unset($getLikedFeedback['user_id'][$userIdKey]);
                            $getLikedFeedback['user_id'] = array_values($getLikedFeedback['user_id']);
                            $updatedLikedFeedback = json_encode($getLikedFeedback);
                            $checkFeedback->user_liked = $updatedLikedFeedback;
                            $checkFeedback->save();
                        }
                    }

                    if( !empty( $getDisLikedFeedback ) ) {
                        $getDisLikedFeedback = json_decode($getDisLikedFeedback, true);
                        if( !in_array($userId, $getDisLikedFeedback['user_id']) ) {
                            // Add $userId to the array
                            $getDisLikedFeedback['user_id'][] = $userId;

                            // Convert the array back to JSON
                            $updatedDisLikedFeedback = json_encode($getDisLikedFeedback);
                            $checkFeedback->user_disliked = $updatedDisLikedFeedback;

                            if( $checkFeedback->save() ) {
                                $message = "Disliked successfully.";
                                $errors = "";

                                $userNotificationData = [
                                    'sender_user_id' => $userId,
                                    'receiver_user_id' => $getToUserFeedback,
                                    'notification_message' => $user->name." has disliked your feedback.",
                                    'notification_type' => 2,
                                ];
                                UserNotifications::create($userNotificationData);
                            } else {
                                $message = "";
                                $errors = "Somethig went wrong!";
                            }
                            return response()->json(
                            [
                                'errors' => $errors,
                                "status" => 200,
                                "message" => $message,
                            ],200);
                        } else {
                            if( !empty( $getDisLikedFeedback ) ) {
                                if( in_array($userId, $getDisLikedFeedback['user_id']) ) {
                                    $userIdKey = array_search($userId, $getDisLikedFeedback['user_id']);
                                    unset($getDisLikedFeedback['user_id'][$userIdKey]);
                                    $getDisLikedFeedback['user_id'] = array_values($getDisLikedFeedback['user_id']);
                                    $updatedDisLikedFeedback = json_encode($getDisLikedFeedback);
                                    $checkFeedback->user_disliked = $updatedDisLikedFeedback;

                                    if( $checkFeedback->save() ) {
                                        $userNotificationData = [
                                            'sender_user_id' => $userId,
                                            'receiver_user_id' => $getToUserFeedback,
                                            'notification_message' => $user->name." has disliked your feedback.",
                                            'notification_type' => 2,
                                        ];
                                        UserNotifications::create($userNotificationData);
                                        $message = "Disliked successfully.";
                                        $errors = "";
                                    } else {
                                        $message = "";
                                        $errors = "Somethig went wrong!";
                                    }
                                    return response()->json(
                                    [
                                        'errors' => $errors,
                                        "status" => 200,
                                        "message" => $message,
                                    ],200);
                                }
                            } else {
                                return response()->json(
                                [
                                    'errors' => '',
                                    "status" => 200,
                                    "message" => 'Disliked not removed.'
                                ],200);
                            }
                        }
                    } else {
                        $userDisLiked = ['user_id' => array($userId)];
                        $checkFeedback->user_disliked = json_encode($userDisLiked);

                        if( $checkFeedback->save() ) {
                            $userNotificationData = [
                                'sender_user_id' => $userId,
                                'receiver_user_id' => $getToUserFeedback,
                                'notification_message' => $user->name." has disliked your feedback.",
                                'notification_type' => 2,
                            ];
                            UserNotifications::create($userNotificationData);
                            $message = "Disliked successfully.";
                            $errors = "";
                        } else {
                            $message = "";
                            $errors = "Somethig went wrong!";
                        }
                        return response()->json(
                        [
                            'errors' => $errors,
                            "status" => 200,
                            "message" => $message,
                        ],200);
                    }
                    break;
                default:
                    return response()->json(['errors' => 'Please provide like type.'],422);
                    break;
            }

        } else {
            return response()->json(['errors' => 'User is not logged in'],401);
        }
    }

    /**
     * Like user feedback.(Not in work)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function likeFeedback(Request $request)
    {
        $messaging = app('firebase.messaging');
        if(auth('sanctum')->check()) {
            $validator = Validator::make($request->all(), [
                'feedback_id' => 'required|exists:rating,id',
                'user_id' => 'required|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $feedbackId = $request->feedback_id;
            $userId = $request->user_id;

            $checkFeedback = Feedback::where('id', $feedbackId)->first();
            $getLikedFeedback = $checkFeedback->user_liked;
            $getToUserFeedback = $checkFeedback->to_userid;
            $getToUser = User::where('id', $getToUserFeedback)->first();

            if( $getLikedFeedback ) {
                $getLikedFeedback = json_decode($getLikedFeedback, true);
                if( !in_array($userId, $getLikedFeedback['user_id']) ) {
                    // Add $userId to the array
                    $getLikedFeedback['user_id'][] = $userId;

                    // Convert the array back to JSON
                    $updatedLikedFeedback = json_encode($getLikedFeedback);
                    $checkFeedback->user_liked = $updatedLikedFeedback;

                    if( $checkFeedback->save() ) {
                        $message = "Liked successfully.";
                        $errors = "";

                        $user = User::where('id', $userId)->first();
                        try {
                            $receiverDeviceToken = $getToUser->device_token;
                            if( !empty($receiverDeviceToken) ) {
                                $deviceToken = $receiverDeviceToken;

                                $messageApp = CloudMessage::fromArray([
                                    'token' => $deviceToken,
                                    'notification' => [
                                        "title" => "Like your feedback",
                                        "body" => $user->name." has liked your feedback.",
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
                                        "title" => "Like your feedback",
                                        "body" => $user->name." has liked your feedback.",
                                        "type" => 'like_feedback',
                                        "sender_id" => $userId
                                    ],
                                ]);
                                // dd($messageApp);
                                $messaging->send($messageApp);
                            }

                            $userNotificationData = [
                                'sender_user_id' => $userId,
                                'receiver_user_id' => $getToUserFeedback,
                                'notification_message' => $user->name." has liked your feedback.",
                                'notification_type' => 1,
                            ];
                            UserNotifications::create($userNotificationData);
                        } catch (\Exception $e) {
                            // $data['exception_error'] = $e->getMessage();
                            return response()->json(['errors' => $e->getMessage(), 'status' => 200, 'message' => 'Add to favourite'], 200);
                        }
                    } else {
                        $message = "";
                        $errors = "Somethig went wrong!";
                    }
                    return response()->json(
                    [
                        'errors' => $errors,
                        "status" => 200,
                        "message" => $message,
                    ],200);
                } else {
                    return response()->json(
                    [
                        'errors' => '',
                        "status" => 200,
                        "message" => 'Alredy liked.'
                    ],200);
                }
            } else {
                $userLiked = ['user_id' => array($userId)];
                $checkFeedback->user_liked = json_encode($userLiked);

                if( $checkFeedback->save() ) {
                    $message = "Liked successfully.";
                    $errors = "";

                    try {
                        $receiverDeviceToken = $getToUser->device_token;
                        if( !empty($receiverDeviceToken) ) {
                            $deviceToken = $receiverDeviceToken;

                            $messageApp = CloudMessage::fromArray([
                                'token' => $deviceToken,
                                'notification' => [
                                    "title" => "Like your feedback",
                                    "body" => $user->name." has liked your feedback.",
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
                                    "title" => "Like your feedback",
                                    "body" => $user->name." has liked your feedback.",
                                    "type" => 'like_feedback',
                                    "sender_id" => $userId
                                ],
                            ]);
                            // dd($messageApp);
                            $messaging->send($messageApp);
                        }

                        $userNotificationData = [
                            'sender_user_id' => $userId,
                            'receiver_user_id' => $getToUserFeedback,
                            'notification_message' => $user->name." has liked your feedback.",
                            'notification_type' => 1,
                        ];
                        UserNotifications::create($userNotificationData);
                    } catch (\Exception $e) {
                        // $data['exception_error'] = $e->getMessage();
                        return response()->json(['errors' => $e->getMessage(), 'status' => 200, 'message' => 'Add to favourite'], 200);
                    }
                } else {
                    $message = "";
                    $errors = "Somethig went wrong!";
                }
                return response()->json(
                [
                    'errors' => $errors,
                    "status" => 200,
                    "message" => $message,
                ],200);
            }
        } else {
            return response()->json(['errors' => 'User is not logged in'],401);
        }
    }

    /**
     * Dislike user feedback.(Not in work)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function dislikeFeedback(Request $request)
    {
        if(auth('sanctum')->check()) {
            $validator = Validator::make($request->all(), [
                'feedback_id' => 'required|exists:rating,id',
                'user_id' => 'required|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $feedbackId = $request->feedback_id;
            $userId = $request->user_id;
            $checkFeedback = Feedback::where('id', $feedbackId)->first();
            $getDisLikedFeedback = $checkFeedback->user_disliked;
            $getToUserFeedback = $checkFeedback->to_userid;
            $getToUser = User::where('id', $getToUserFeedback)->first();
            $user = User::where('id', $userId)->first();

            if( $getDisLikedFeedback ) {
                $getDisLikedFeedback = json_decode($getDisLikedFeedback, true);
                if( !in_array($userId, $getDisLikedFeedback['user_id']) ) {
                    $userIdKey = array_search($userId, $getDisLikedFeedback['user_id']);
                    if( !$userIdKey ) {
                        unset($getDisLikedFeedback['user_id'][$userIdKey]);
                        $getDisLikedFeedback['user_id'] = array_values($getDisLikedFeedback['user_id']);
                    }
                    $getDisLikedFeedback['user_id'][] = $userId;
                    $updatedLikedFeedback = json_encode($getDisLikedFeedback);
                    // dd($updatedLikedFeedback);
                    $checkFeedback->user_disliked = $updatedLikedFeedback;
                    if( $checkFeedback->save() ) {
                        $userNotificationData = [
                            'sender_user_id' => $userId,
                            'receiver_user_id' => $getToUserFeedback,
                            'notification_message' => $user->name." has disliked your feedback.",
                            'notification_type' => 1,
                        ];
                        UserNotifications::create($userNotificationData);
                        $message = "Disliked successfully.";
                        $errors = "";
                    } else {
                        $message = "";
                        $errors = "Somethig went wrong!";
                    }
                    return response()->json(
                    [
                        'errors' => $errors,
                        "status" => 200,
                        "message" => $message,
                    ],200);
                } else {
                    return response()->json(
                    [
                        'errors' => '',
                        "status" => 200,
                        "message" => 'Alredy disliked.'
                    ],200);
                }
            } else { //Return no data for dislike

            }
        } else {
            return response()->json(['errors' => 'User is not logged in'],401);
        }
    }
}
