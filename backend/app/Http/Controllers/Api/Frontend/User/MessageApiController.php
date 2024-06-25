<?php

namespace App\Http\Controllers\Api\Frontend\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Auth\User;
use App\Models\Message;
use App\Models\Conversation;
use App\Models\Sitesettings;
use App\Models\Feedback;
use App\Models\Spskill;

use App\Events\MessageSent;
use App\Events\PrivateMessageSent;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Auth\SocialAccount;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Cache;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

use Kreait\Firebase\Factory;
use \Kreait\Firebase\Contract\Messaging;
use \Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Kreait\Firebase\Messaging\AndroidConfig;

use App\Models\ChMessage;
use App\Models\ChFavorite;
use App\Models\ChCoversation;
use Chatify\Facades\ChatifyMessenger as Chatify;

class MessageApiController extends Controller
{

    public function __construct()
    {
        // $this->middleware('sanctum');
        $this->middleware('auth:sanctum');
    }

    public function inbox(Request $request) {
        if(auth('sanctum')->check()) {
            $validation = Validator::make($request->all(), [
                'uid'   => 'required|exists:users,id',
            ]);

            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()]);
            }

            $userId = $request->uid;
            DB::enableQueryLog();
            // $message_profile_list =  Message::select(DB::raw('count(`messages`.`receiver_id`) AS chatCount,GROUP_CONCAT(`messages`.`receiver_id`) AS ChtGrp, messages.user_id AS msgUserId, messages.receiver_id AS msgReceiverId, users.id,users.first_name,users.last_name,users.email,users.avatar_type,users.avatar_location'))
            // ->join('users', 'messages.receiver_id', '=', 'users.id')
            // ->where([
            //     ['messages.user_id', '=', $userId],
            //     // ['messages.receiver_id', '<>', $userId],
            // ])
            // ->orWhere([
            //     ['messages.receiver_id', '=', $userId],
            // ])
            // ->groupBy('messages.receiver_id')
            // //->toSql();
            // ->orderBy('messages.created_at', 'desc')
            // ->get()->toArray();

            // Main query to get the user profile list and the last message for each conversation
            $excludedConversationIds = [63, 111];
            DB::enableQueryLog();
            $message_profile_list = Message::select(DB::raw('COUNT(messages.receiver_id) AS chatCount, GROUP_CONCAT(`messages`.`receiver_id`) AS ChtGrp, messages.user_id AS msgUserId, messages.receiver_id AS msgReceiverId, messages.conversation_id as conversation_id, conversation.deleted_by as deleted_by'))
                ->selectRaw('CASE WHEN messages.user_id = '.$userId.' THEN messages.receiver_id ELSE messages.user_id END AS id')
                ->selectRaw('CASE WHEN messages.user_id = '.$userId.' THEN  (SELECT first_name  FROM users WHERE users.id = messages.receiver_id) ELSE (SELECT first_name  FROM users WHERE users.id = messages.user_id) END AS first_name')
                ->selectRaw('CASE WHEN messages.user_id = '.$userId.' THEN  (SELECT last_name  FROM users WHERE users.id = messages.receiver_id) ELSE (SELECT last_name  FROM users WHERE users.id = messages.user_id) END AS last_name')
                ->selectRaw('CASE WHEN messages.user_id = '.$userId.' THEN  (SELECT email  FROM users WHERE users.id = messages.receiver_id) ELSE (SELECT email  FROM users WHERE users.id = messages.user_id) END AS email')
                ->selectRaw('CASE WHEN messages.user_id = '.$userId.' THEN  (SELECT avatar_type  FROM users WHERE users.id = messages.receiver_id) ELSE (SELECT avatar_type  FROM users WHERE users.id = messages.user_id) END AS avatar_type')
                ->selectRaw('CASE WHEN messages.user_id = '.$userId.' THEN  (SELECT avatar_location  FROM users WHERE users.id = messages.receiver_id) ELSE (SELECT avatar_location  FROM users WHERE users.id = messages.user_id) END AS avatar_location')
                // ->fromSub($subquery, 'lastMessage') // Use fromSub() to join the subquery result as a table
                ->leftJoin('users', 'messages.receiver_id', '=', 'users.id')
                ->leftJoin('conversation', 'conversation.id', '=', 'messages.conversation_id')
                ->join('users AS users_receiver', 'messages.user_id', '=', 'users_receiver.id')
                ->where(function ($query) use ($userId, $excludedConversationIds) {
                    $query->where('messages.user_id', $userId)
                        ->orWhere('messages.receiver_id', $userId);
                })
                // ->where([
                //     ['messages.user_id', '=', $userId],
                // ])
                // ->orWhere([
                //     ['messages.receiver_id', '=', $userId],
                // ])
                // ->whereNotIn('messages.conversation_id', $conIds)
                ->groupBy('id')
                ->orderByDesc(DB::raw('MAX(messages.created_at)'))
                ->get()
                ->toArray();
                // dd(DB::getQueryLog());
            foreach( $message_profile_list as $key => $getMsgUser ) {
                $msgUserId = $getMsgUser['msgUserId'];
                $msgReceiverId = $getMsgUser['msgReceiverId'];
                DB::enableQueryLog();
                $getLastMessage = Message::select('id', 'message', 'readStatus', 'created_at', 'user_id', 'receiver_id')
                ->where(function ($query) use ($msgUserId, $msgReceiverId) {
                    $query->where([
                        ['user_id', '=', $msgUserId],
                        ['receiver_id', '=', $msgReceiverId],
                    ])->orWhere([
                        ['user_id', '=', $msgReceiverId],
                        ['receiver_id', '=', $msgUserId],
                    ]);
                })
                ->orderBy('created_at', 'desc')
                ->first();

                $message_profile_list[$key]['lastMessageFlg'] = $msgReceiverId;

                if ($getLastMessage) {
                    $message_profile_list[$key]['lastMessage'] = $getLastMessage->message;
                    $message_profile_list[$key]['readStatus'] = $getLastMessage->readStatus;
                    $message_profile_list[$key]['lastMessageTime'] = $getLastMessage->created_at->format('Y-m-d H:i:s');

                    // Get the sender ID from the message
                    $senderId = $getLastMessage->user_id;
                    $message_profile_list[$key]['senderId'] = $senderId;
                } else {
                    $message_profile_list[$key]['lastMessage'] = '';
                    $message_profile_list[$key]['lastMessageTime'] = '';
                }

                $userAverageRating = Feedback::user_average_rating($msgReceiverId);
                $userAverageRating = round($userAverageRating);
                $message_profile_list[$key]['userRating'] = $userAverageRating;
            }
            // dd(DB::getQueryLog());
            return response()->json(
            [
                'errors' => '',
                "status" => 200,
                "message" => "Success",
                "CurrentChats" => $message_profile_list,
            ],200);

        } else {
            return response()->json(['errors' => 'User is not logged in'],401);
        }
    }

    public function fetchChat(Request $request)
    {
        if(auth('sanctum')->check()) {
            $validation = Validator::make($request->all(), [
                'uid'   => 'required|exists:users,id',
            ]);

            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()]);
            }

            $message_profile_list =  Message::select(DB::raw('count(`messages`.`receiver_id`) AS chatCount,GROUP_CONCAT(`messages`.`receiver_id`) AS ChtGrp, users.id,users.first_name,users.last_name,users.email,users.avatar_type,users.avatar_location'))
            ->join('users', 'messages.receiver_id', '=', 'users.id')
            ->where([
                        ['messages.user_id', '=', auth()->id()],
                        // ['messages.receiver_id', '<>', auth()->id()],
                    ])
            ->orWhere([
                    ['messages.receiver_id', '=', auth()->id()],
                ])
            ->groupBy('messages.receiver_id')
            //->toSql();
            ->get()->toArray();

            $message_data           =   $message_profile_list;
            $message_data_count     =   0;
            foreach($message_data as $message_value){
                //$online_result      = Cache::put('user-online-'.$message_value->id, true,'');
                $online_result      = Cache::has('user-is-online-'.$message_value['id']);
                $online_result      = (!empty($online_result)) ? 1 : 0;

                $message_data[$message_data_count]['online_status'] = $online_result;
                $count = Message::where('user_id' ,'=',$message_value['id'])->where('receiver_id','=',auth()->id())->where('readStatus','=',0)->get();

                $message_data[$message_data_count]['msg_result'] = $count->count();
                $message_data_count++;
            }

            return response()->json(
            [
                'errors' => '',
                "status" => 200,
                "message" => "Success",
                "UserChats" => $message_data,
            ],200);

        } else {
            return response()->json(['errors' => 'User is not logged in'],401);
        }
    }

    public function SearchChatUsers(Request $request)
    {
        if(auth('sanctum')->check()) {
            $validation = Validator::make($request->all(), [
                'uid'   => 'required|exists:users,id',
                // 'search_user'   => 'required',
            ]);

            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()]);
            }

            $search_user = $request->search_user;
            if( !empty( $search_user ) ) {
                $message_profile_list =  Message::select(DB::raw('count(`messages`.`receiver_id`) AS chatCount,GROUP_CONCAT(`messages`.`receiver_id`) AS ChtGrp, users.id,users.first_name,users.last_name,users.email,users.avatar_type,users.avatar_location'))
                    ->join('users', 'messages.receiver_id', '=', 'users.id')
                    ->where([
                                ['messages.user_id', '=', auth()->id()],
                                ['messages.receiver_id', '<>', auth()->id()],
                            ])
                    ->Where(function ($query) use ($search_user) {
                            $query->orWhere('users.first_name', 'LIKE', "%".$search_user."%")
                            ->orWhere('users.last_name', 'LIKE', "%".$search_user."%")
                            ->orWhere('users.email', 'LIKE', "%".$search_user."%")
                            ->Where('messages.receiver_id', '<>', auth()->id());
                    })
                    ->groupBy('messages.receiver_id')
                    //->toSql();
                    ->get()->toArray();
            } else {
                $message_profile_list =  Message::select(DB::raw('count(`messages`.`receiver_id`) AS chatCount,GROUP_CONCAT(`messages`.`receiver_id`) AS ChtGrp, users.id,users.first_name,users.last_name,users.email,users.avatar_type,users.avatar_location'))
                ->join('users', 'messages.receiver_id', '=', 'users.id')
                ->where([
                            ['messages.user_id', '=', auth()->id()],
                            ['messages.receiver_id', '<>', auth()->id()],
                        ])
                ->groupBy('messages.receiver_id')
                // ->toSql();
                ->get()->toArray();
                // echo "<pre>";print_r($message_profile_list); echo "</pre>";
            }
            $message_data           =   $message_profile_list;
            $message_data_count     =   0;
            foreach($message_data as $message_value){
                //$online_result      = Cache::put('user-online-'.$message_value->id, true,'');
                $online_result      = Cache::has('user-is-online-'.$message_value['id']);
                $online_result      = (!empty($online_result)) ? 1 : 0;

                $message_data[$message_data_count]['online_status'] = $online_result;
                $count = Message::where('user_id' ,'=',$message_value['id'])
                        ->where('receiver_id','=',auth()->id())->where('readStatus','=',0)->get();
                // dd($count->count());
                $message_data[$message_data_count]['msg_result'] = $count->count();
                $message_data_count++;
            }
            return response()->json(
            [
                'errors' => '',
                "status" => 200,
                "message" => "Success",
                "SearchResult" => $message_data,
            ],200);
        }  else {
            return response()->json(['errors' => 'User is not logged in'],401);
        }
    }

    public function getChatUserDetails(Request $request)
    {
        // echo "<pre>";print_r($request->all()); echo "</pre>"; die;

        if(auth('sanctum')->check()) {
            $validation = Validator::make($request->all(), [
                'uid'   => 'required|exists:users,id',
                'reuser'   => 'required|exists:users,id',
            ]);

            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()]);
            }

            $other          =   array();
            $userId         =   !empty($request->reuser) ? $request->reuser : '';

            $userDetails    =   User::where('id' ,'=',$userId)->get();

            $update = Message::where('receiver_id' ,'=',$request->uid)->where('user_id','=',$userId)->where('readStatus','=',0)->update(['readStatus' => 1]);

            DB::enableQueryLog();
            // $privateCommunication = Message::Orwhere(function($query) use($userId){
            //     $query->Where(['user_id' => $userId, 'receiver_id' => auth()->id()]);

            //     $query->orwhere(function($query) use($userId){
            //         $query->Where(['user_id' => auth()->id(), 'receiver_id' =>$userId ]);
            //     });

            // })->where('deleted_by',"!=" ,auth()->id())
            // ->get();
            $privateCommunication = Message::orWhere(function ($query) use ($userId) {
                $query->Where(['user_id' => $userId, 'receiver_id' => auth()->id()]);

                $query->orWhere(function ($query) use ($userId) {
                    $query->where(['user_id' => auth()->id(), 'receiver_id' => $userId]);
                });
            })
            ->where('deleted_by', '!=', auth()->id())
            ->select([
                '*',
                DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as formatted_created_at"),
                // Add any other columns you want to select from the `messages` table
            ])
            ->get();
            // dd(DB::getQueryLog());
            $user_image     =   "/storage/avatars/dummy.png";

            if($userDetails[0]->avatar_type == "gravatar"){
                $user_image     =   "/storage/avatars/dummy.png";
            }else if ($userDetails[0]->avatar_type == "storage"){
                if(!empty($userDetails[0]->avatar_location)){
                    $user_image =   "/storage/".$userDetails[0]->avatar_location;
                } else {
                    $user_image =   "/storage/avatars/dummy.png";
                }
            }else{
                $social_Account = SocialAccount::where('user_id','=',$userDetails[0]->user_id)->where('provider','=',$userDetails[0]->avatar_type)->first();
                if(!empty($social_Account)){
                    $user_image =   $social_Account->avatar;
                } else {
                    $user_image =   "/storage/avatars/dummy.png";
                }
            }

            $userDetails[0]->image  =   $user_image;

            $other['total_message'] =   $privateCommunication->count();

            $online_result          =   Cache::has('user-is-online-'.$userDetails[0]->id);
            $online_result          =   (!empty($online_result)) ? 1 : 0;
            $other['is_online']     =   $online_result;
            $other['slug']          =   $userDetails[0]->slug;

            if($userDetails[0]->is_sp){
                $other['is_sp'] = 1;
                $other['profile_url'] = "/profile/".$userDetails[0]->slug;
            } else {
                $other['is_sp']         =   0;
            }

            $loginId                =   auth()->id();
            $LoginuserDetails       =   User::where('id' ,'=',$loginId)->get();

            echo "<pre>";print_r($LoginuserDetails); echo "</pre>";

            if($LoginuserDetails[0]->avatar_type == "gravatar"){
                $user_image     =   "/storage/avatars/dummy.png";
            }else if ($LoginuserDetails[0]->avatar_type == "storage"){
                if(!empty($LoginuserDetails[0]->avatar_location)){
                    $user_image =   "/storage/".$LoginuserDetails[0]->avatar_location;
                } else {
                    $user_image =   "/storage/avatars/dummy.png";
                }
            }else{
                $social_Account = SocialAccount::where('user_id','=',$LoginuserDetails[0]->user_id)->where('provider','=',$LoginuserDetails[0]->avatar_type)->first();
                if(!empty($social_Account)){
                    $user_image =   $social_Account->avatar;
                } else {
                    $user_image =   "/storage/avatars/dummy.png";
                }
            }

            $LoginuserDetails[0]->image  =   $user_image;

            return response()->json(
            [
                'errors' => '',
                "status" => 200,
                "message" => "Success",
                'privateCommunication' => $privateCommunication,
                'userDetails' => $userDetails[0],
                'other' => $other,
                'loginUserDetails' => $LoginuserDetails[0]
            ],200);

        } else {
            return response()->json(['errors' => 'User is not logged in'],401);
        }
    }

    public function SendUserMessage(Request $request)
    {
        $messaging = app('firebase.messaging');
        if(auth('sanctum')->check()) {
            $validation = Validator::make($request->all(), [
                'uid'   => 'required|exists:users,id',
                // 'userId'   => 'required|exists:users,id',
                'tempId' => 'required|exists:users,id',
                'chat_message' => 'required',
                'myFile' => 'nullable|mimes:jpeg,jpg,png|max:2048'
            ]);

            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()]);
            }
            $chat_message       =   !empty($request->chat_message) ? $request->chat_message : '';
            $sendId             =   !empty($request->tempId) ? $request->tempId : '';

            $uploadFile         =   !empty($_FILES['myFile']) ? $_FILES['myFile'] : '';

            // $conversation_id    =   Message::select('conversation_id')->where('user_id' ,'=',auth()->id())->where('receiver_id','=',$sendId)->get();
            $conversation_id    =   Conversation::where([
                ['user_one', $request->uid],
                ['user_two', $request->tempId],
            ])->orWhere([
                ['user_one', $request->tempId],
                ['user_two', $request->uid],
            ])->first();

            $conversation_id    =   $conversation_id->id;
            // dd($conversation_id);
            // $conversation_id    =   $conversation_id[0]->conversation_id;

            // dd($conversation_id);

            if(!empty($uploadFile)){

                $uploadFille_name   =   time().'-'.$uploadFile['name'];
                $uploadFille_type   =   !empty($uploadFile['type']) ? $uploadFile['type'] : '';
                $uploadFille_tmp    =   $uploadFile['tmp_name'];
                $uploadFille_size   =   $uploadFile['size'];

                $dir                =   public_path()."/storage/chat/".$uploadFille_name;
                $uploadStatus       =   move_uploaded_file($uploadFille_tmp, $dir);

                if($uploadStatus){
                    // create new message and save it
                    $MessageImage                   = new Message;
                    $MessageImage->user_id          = auth()->id();
                    $MessageImage->receiver_id      = $sendId;
                    $MessageImage->image            = "/storage/chat/".$uploadFille_name;
                    $MessageImage->conversation_id  = $conversation_id;
                    $MessageImage->readStatus       = 0;
                    $MessageImage->save();
                }

            }

            // create new message and save it
            $Message                    = new Message;
            $Message->user_id           = auth()->id();
            $Message->receiver_id       = $sendId;
            $Message->message           = $chat_message;
            $Message->conversation_id   = $conversation_id;
            $Message->readStatus        = 0;

            if($Message->save()){
                try {
                    $receiverData = User::where('id', $sendId)->firstOrFail();

                    $receiverDeviceToken = $receiverData->device_token;

                    if( !empty($receiverDeviceToken) ) {

                        $getDeviceToken = auth()->user();

                        // $deviceTokekApp = $getDeviceToken->device_token;
                        // $deviceToken = $deviceTokekApp;
                        $deviceToken = $receiverDeviceToken;
                        // $deviceToken = 'c_aL384iCUvwsMD-l-JRL3:APA91bE_NiDLEeGXaNFZLFmbYrn2fRu3qvD-jF4Hh1vSxfduHT5orzu1PkqnghQ4qOIQ1lyTgH_o-mHjac0nNIfAtuvrzgIRStKC7bxsFiuJ61TeDqOhnjdJPYbSPIxNjurDjaMLup-5';

                        // $messageApp = CloudMessage::withTarget('token', $deviceToken);

                        // $messageApp = CloudMessage::fromArray([
                        //     'token' => $deviceToken,
                        //     'notification' => [
                        //         "title" => "New Message",
                        //         "body" => $getDeviceToken->name." has sent you a message.",
                        //     ], // optional
                        //     'data' => [
                        //         "type" => 'new_message',
                        //         "sender_id" => auth()->id()
                        //     ],
                        // ]);
                        $messageApp = CloudMessage::fromArray([
                            'token' => $deviceToken,
                            'notification' => [
                                "title" => "New Message",
                                "body" => $getDeviceToken->name." has sent you a message.",
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
                                "body" => $getDeviceToken->name." has sent you a message.",
                                "type" => 'new_message',
                                "sender_id" => auth()->id()
                            ],
                        ]);
                        // dd($messageApp);
                        $messaging->send($messageApp);
                    }
                } catch (\Exception $e) {
                    return response()->json(['errors' => $e->getMessage(), 'sucess' => 1, 'message' => 'Message Send Succesfully'], 200);
                }
                return response()->json(['sucess' => 1,'message' => 'Message Send Succesfully']);
            } else {
                return response()->json(['error' => 0,'message' => 'Message Send failed']);
            }

        } else {
            return response()->json(['errors' => 'User is not logged in'],401);
        }
    }

    public function sendQuickMessage(Request $request)
    {
        if(auth('sanctum')->check()) {
            $validation = Validator::make($request->all(), [
                'uid'   => 'required|exists:users,id',
                'profile_id' => 'required|exists:users,id',
                'message' => 'required',
            ]);

            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()]);
            }

            $getCountConvertation = Conversation::where('user_one', $request->profile_id)->where('user_two', auth()->id())->count();

            if( $getCountConvertation > 0 ) {

                $conversationdata   =   Conversation::where('user_one', $request->profile_id)->where('user_two', auth()->id())->firstOrFail();
                $chat_message       =   !empty($request->message) ? $request->message : '';
                $sendId             =   !empty($request->profile_id) ? $request->profile_id : '';

                // create new message and save it
                $Message                    = new Message;
                $Message->user_id           = auth()->id();
                $Message->receiver_id       = $sendId;
                $Message->message           = $chat_message;
                $Message->conversation_id   = $conversationdata->id;
                $Message->readStatus        = 0;

                if($Message->save()){
                    return response()->json(['sucess' => 1,'message' => 'Message Send Succesfully']);
                } else {
                    return response()->json(['error' => 0,'message' => 'Message Send failed']);
                }

            } else {
                $input = [
                    'user_one' => $request->profile_id,
                    'user_two' => auth()->id(),
                ];

                $product = new Conversation;
                $product->user_one = $request->profile_id;
                $product->user_two = auth()->id();
                $product->save();
                $getConversation = $product->id;

                $chat_message       =   !empty($request->message) ? $request->message : '';
                $sendId             =   !empty($request->profile_id) ? $request->profile_id : '';

                $Message                    = new Message;
                $Message->user_id           = auth()->id();
                $Message->receiver_id       = $sendId;
                $Message->message           = $chat_message;
                $Message->conversation_id   = $getConversation;
                $Message->readStatus        = 0;

                if($Message->save()){
                    return response()->json(['sucess' => 1,'message' => 'Message Send Succesfully']);
                } else {
                    return response()->json(['error' => 0,'message' => 'Message Send failed']);
                }
            }
        } else {
            return response()->json(['errors' => 'User is not logged in'],401);
        }
    }

    public function newsendQuickMessage(Request $request)
    {
        if(auth('sanctum')->check()) {
            $validation = Validator::make($request->all(), [
                'id' => 'required|exists:users,id',
                'message' => 'required',
            ]);

            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()]);
            }

            $error = (object)[
                'status' => 0,
                'message' => null
            ];
            if (!$error->status) {
                // send to database
                $message = Chatify::newMessage([
                    'type' => $request['type'],
                    'from_id' => Auth::user()->id,
                    'to_id' => $request['id'],
                    'body' => htmlentities(trim($request['message']), ENT_QUOTES, 'UTF-8'),
                    'attachment' => null,
                ]);

                // fetch message to send it with the response
                $messageData = Chatify::parseMessage($message);

                // send to user using pusher
                if (Auth::user()->id != $request['id']) {
                    Chatify::push("private-chatify.".$request['id'], 'messaging', [
                        'from_id' => Auth::user()->id,
                        'to_id' => $request['id'],
                        'message' => $messageData
                    ]);
                }
                return response()->json([
                    'status' => 1,
                    'error' => $error,
                    'message' => $messageData ?? [],
                    'tempID' => $request['temporaryMsgId'],
                ], 200);
            }


        } else {
            return response()->json(['errors' => 'User is not logged in'],401);
        }
    }

    public function destroyMessage(Request $request)
    {
        if(auth('sanctum')->check()) {
            $validation = Validator::make($request->all(), [
                'uid'   => 'required|exists:users,id',
                'conversation_id'   => 'required|exists:conversation,id',
            ]);

            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()]);
            }

            $uid = array($request->uid);
            $conversation_id = $request->conversation_id;
            DB::enableQueryLog();
            $getConversation = Conversation::find($conversation_id);

            if( empty($getConversation->deleted_by) ) {
                $getConversation->deleted_by = json_encode($uid);
                $deleteMessage = $getConversation->save();

                if( $deleteMessage ) {
                    $message = "Messages deleted successfully.";
                    $errors = array();
                    $status = 200;
                } else {
                    $message = "";
                    $errors = "Oops! Something wrong, messages not delete.";
                    $status = 200;
                }
            } else {
                $getMessages = Message::where('conversation_id', $conversation_id)->delete();
                $getConversation = Conversation::where('id', $conversation_id)->delete();
                $message = "Messages deleted successfully.";
                $errors = array();
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
}
