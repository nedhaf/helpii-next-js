<?php

namespace App\Http\Controllers\vendor\Chatify\Api;

use App\Events\MessageSent;
use App\Events\MessageReceive;
use App\Events\ReceivedMessageSeen;
use App\Events\MessageSeen;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use App\Models\ChMessage as Message;
use App\Models\ChFavorite as Favorite;
use App\Models\ChCoversation;
use Chatify\Facades\ChatifyMessenger as Chatify;
use App\Models\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Feedback;


class MessagesController extends Controller
{
    protected $perPage = 30;

     /**
     * Authinticate the connection for pusher
     *
     * @param Request $request
     * @return void
     */
    public function pusherAuth(Request $request)
    {
        $chatifyPusherAuth =  Chatify::pusherAuth(
            $request->user(),
            Auth::user(),
            $request['channel_name'],
            $request['socket_id']
        );
        return response()->json([
            'status' => 200,
            'result' => json_decode($chatifyPusherAuth, true),
        ]);
    }

    /**
     * Fetch data by id for (user/group)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function idFetchData(Request $request)
    {
        if(auth('sanctum')->check()) {

            // return auth()->user();
            // Favorite
            $favorite = Chatify::inFavorite($request['id']);
            // User data
            $fetch = null;
            $userAvatar = null;
            $status = 'success';
            $message = 'No data';
            if($favorite){
                $fetch = User::where('id', $request['id'])->first();
                if( $fetch->avatar_type == 'storage' ) {
                    $profile_avatar = ($fetch->avatar_location) ? $fetch->avatar_location : 'avatars/dummy.png';
                    $userAvatar = "/storage/".$profile_avatar;
                } else {
                    $userAvatar = Chatify::getUserWithAvatar($fetch)->avatar;
                }
                $message = 'Data found';
            }else{
                $fetch = User::where('id', $request['id'])->first();
                if( $fetch->avatar_type == 'storage' ) {
                    $profile_avatar = ($fetch->avatar_location) ? $fetch->avatar_location : 'avatars/dummy.png';
                    $userAvatar = "/storage/".$profile_avatar;
                } else {
                    $userAvatar = Chatify::getUserWithAvatar($fetch)->avatar;
                }
                $message = 'Data found';
            }

            // send the response
            return response()->json([
                'status' => $status,
                'message' => $message,
                'favorite' => $favorite,
                'fetch' => $fetch,
                'user_avatar' => $userAvatar,
            ]);
        } else {
            return response()->json([
                'status' => 'success',
                'message' => 'User is not logged in',
                'favorite' => null,
                'fetch' => null,
                'user_avatar' => null,
            ]);
        }
    }

    /**
     * This method to make a links for the attachments
     * to be downloadable.
     *
     * @param string $fileName
     * @return \Illuminate\Http\JsonResponse
     */
    public function download($fileName)
    {
        $path = config('chatify.attachments.folder') . '/' . $fileName;
        if (Chatify::storage()->exists($path)) {
            return response()->json([
                'file_name' => $fileName,
                'download_path' => Chatify::storage()->url($path)
            ], 200);
        } else {
            return response()->json([
                'message'=>"Sorry, File does not exist in our server or may have been deleted!"
            ], 404);
        }
    }

    /**
     * Send a message to database
     *
     * @param Request $request
     * @return JSON response
     */
    public function send(Request $request)
    {
        // default variables
        $error = (object)[
            'status' => 0,
            'message' => null
        ];
        $attachment = null;
        $attachment_title = null;

        if(auth('sanctum')->check()) {
            // if there is attachment [file]
            if ($request->hasFile('file')) {
                // allowed extensions
                $allowed_images = Chatify::getAllowedImages();
                $allowed_files  = Chatify::getAllowedFiles();
                $allowed        = array_merge($allowed_images, $allowed_files);

                $file = $request->file('file');
                // check file size
                if ($file->getSize() < Chatify::getMaxUploadSize()) {
                    $dir = public_path()."/storage/attachments/";
                    if (in_array(strtolower($file->extension()), $allowed)) {
                        // get attachment name
                        $attachment_title = $file->getClientOriginalName();
                        // upload attachment and store the new name
                        $attachment = Str::uuid() . "." . $file->extension();
                        $file->storeAs(config('chatify.attachments.folder'), $attachment, config('chatify.storage_disk_name'));
                        $file->move($dir ."/", $attachment);
                    } else {
                        $error->status = 1;
                        $error->message = "File extension not allowed!";
                    }
                } else {
                    $error->status = 1;
                    $error->message = "File size you are trying to upload is too large!";
                }
            }

            if (!$error->status) {
                // send to database
                $message = Chatify::newMessage([
                    'type' => $request['type'],
                    'from_id' => Auth::user()->id,
                    'to_id' => $request['id'],
                    'body' => htmlentities(trim($request['message']), ENT_QUOTES, 'UTF-8'),
                    'attachment' => ($attachment) ? json_encode((object)[
                        'new_name' => $attachment,
                        'old_name' => htmlentities(trim($attachment_title), ENT_QUOTES, 'UTF-8'),
                    ]) : null,
                ]);

                // fetch message to send it with the response
                $messageData = Chatify::parseMessage($message);

                // send to user using pusher
                if (Auth::user()->id != $request['id']) {

                    // Chatify::push("private-chatify.".$request['id'], 'messaging', [
                    //     'from_id' => Auth::user()->id,
                    //     'to_id' => $request['id'],
                    //     'message' => $messageData
                    // ]);
                    // Chatify::push("private-Chat.User.".$request['id'], 'MessageSent',[
                    //     'from_id' => Auth::user()->id,
                    //     'to_id' => $request['id'],
                    //     'message' => $messageData
                    // ]);

                }
            }
            broadcast(new MessageSent($messageData))->toOthers();
            broadcast(new MessageReceive($messageData))->toOthers();
            // send the response
            return response()->json([
                'status' => '200',
                'error' => $error,
                'message' => $messageData ?? [],
                'tempID' => $request['temporaryMsgId'],
            ]);
        } else {
            return response()->json([
                'status' => '200',
                'error' => $error,
                'message' => [],
                'tempID' => null,
            ]);
        }
    }

    /**
     * fetch [user/group] messages from database
     *
     * @param Request $request
     * @return JSON response
     */
    public function fetch(Request $request)
    {
        if(auth('sanctum')->check()) {
            $query = Chatify::fetchMessagesQuery($request['id'])->latest();
            $messages = $query->paginate($request->per_page ?? $this->perPage);
            $totalMessages = $messages->total();
            $lastPage = $messages->lastPage();
            $userData = User::where('id', $request['id'])->first();
            $response = [
                'status' => 'success',
                'message' => 'data found',
                'total' => $totalMessages,
                'last_page' => $lastPage,
                'last_message_id' => collect($messages->items())->last()->id ?? null,
                'userData' => $userData,
                'messages' => $messages->items(),
            ];
            return response()->json($response);
        } else {
            return response()->json([
                'status' => 'success',
                'message' => 'User is not logged in',
                'total' => null,
                'last_page' => null,
                'last_message_id' => null,
                'messages' => null,
            ]);
        }
    }

    /**
     * Default fetch messages query between a Sender and Receiver.
     *
     * @param int $user_id
     * @return Message|\Illuminate\Database\Eloquent\Builder
     */
    public function fetchMessagesQuery($user_id)
    {
        $fetchMessage =  Message::where('from_id', Auth::user()->id)->where('to_id', $user_id)->orWhere('from_id', $user_id)->where('to_id', Auth::user()->id);
        return $fetchMessage;
    }

    /**
     * Make messages as seen
     *
     * @param Request $request
     * @return void
     */
    public function seen(Request $request)
    {
        if(auth('sanctum')->check()) {
            // make as seen
            $user_id = $request['id'];
            $seen = Chatify::makeSeen($request['id']);
            // send the response

            $messageData = array(
                'to_id' => $request['id']
            );


            $messageData2 = array(
                'from_id' => Auth::user()->id
            );

            broadcast(new MessageSeen($messageData))->toOthers();
            broadcast(new ReceivedMessageSeen($messageData2));

            return response()->json([
                'status' => $seen,
                'message' => 'success',
            ], 200);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'User is not logged in',
            ], 200);
        }
    }

    /**
     * Get contacts list
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse response
     */
    public function getContacts(Request $request)
    {
        // get all users that received/sent message from/to [Auth user]
        DB::enableQueryLog();
        $loggedInUserId = Auth::user()->id;
        $users = Message::join('users',  function ($join) {
            $join->on('ch_messages.from_id', '=', 'users.id')
                ->orOn('ch_messages.to_id', '=', 'users.id');
        })
        ->where(function ($q) {
            $q->where('ch_messages.from_id', Auth::user()->id)
            ->orWhere('ch_messages.to_id', Auth::user()->id);
        })
        ->where('users.id','!=',Auth::user()->id)
        ->where(function ($query) {
            $query->whereNull('ch_messages.deleted_by')
            ->orWhereJsonDoesntContain('ch_messages.deleted_by', Auth::user()->id);
        })
        ->select('users.*', DB::raw('MAX(ch_messages.created_at) as max_created_at'))
        ->orderBy('max_created_at', 'desc')
        ->groupBy('users.id')
        ->paginate($request->per_page ?? $this->perPage);

        foreach( $users as $k => $contact ) {
            // get last message
            $lastMessage = Chatify::getLastMessageQuery($contact->id);
            // Get Unseen messages counter
            $unseenCounter = Chatify::countUnseenMessages($contact->id);

            // Get user rating stars
            $userAverageRating = Feedback::user_average_rating($contact->id);
            $userAverageRating = round($userAverageRating);

            if ($lastMessage) {
                $lastMessage->created_at = $lastMessage->created_at->toIso8601String();
                $lastMessage->timeAgo = $lastMessage->created_at->diffForHumans();
            }

            $users[$k]['userRating'] = $userAverageRating;
            $users[$k]['unseenCounter'] = $unseenCounter;
            $users[$k]['lastMessage'] = $lastMessage;
        }

        return response()->json([
            'contacts' => $users->items(),
            'total' => $users->total() ?? 0,
            'last_page' => $users->lastPage() ?? 1,
        ], 200);
    }

    public function getTotalUnseenMessages()
    {
        $loggedInUserId = Auth::user()->id;

        // Fetch all users that the authenticated user has exchanged messages with
        $users = Message::join('users', function ($join) {
                $join->on('ch_messages.from_id', '=', 'users.id')
                    ->orOn('ch_messages.to_id', '=', 'users.id');
            })
            ->where(function ($q) {
                $q->where('ch_messages.from_id', Auth::user()->id)
                    ->orWhere('ch_messages.to_id', Auth::user()->id);
            })
            ->where('users.id', '!=', Auth::user()->id)
            ->where(function ($query) {
                $query->whereNull('ch_messages.deleted_by')
                    ->orWhereJsonDoesntContain('ch_messages.deleted_by', Auth::user()->id);
            })
            ->select('users.id')
            ->distinct()
            ->get();

        $totalUnseenMessages = 0;

        // Iterate through each user and calculate the total unseen messages
        foreach ($users as $user) {
            $totalUnseenMessages += Chatify::countUnseenMessages($user->id);
        }


        return $totalUnseenMessages;
    }

    public function getTotalUnseenMessagesApi()
    {
        $loggedInUserId = Auth::user()->id;

        // Fetch all users that the authenticated user has exchanged messages with
        $users = Message::join('users', function ($join) {
                $join->on('ch_messages.from_id', '=', 'users.id')
                    ->orOn('ch_messages.to_id', '=', 'users.id');
            })
            ->where(function ($q) {
                $q->where('ch_messages.from_id', Auth::user()->id)
                    ->orWhere('ch_messages.to_id', Auth::user()->id);
            })
            ->where('users.id', '!=', Auth::user()->id)
            ->where(function ($query) {
                $query->whereNull('ch_messages.deleted_by')
                    ->orWhereJsonDoesntContain('ch_messages.deleted_by', Auth::user()->id);
            })
            ->select('users.id')
            ->distinct()
            ->get();

        $totalUnseenMessages = 0;

        // Iterate through each user and calculate the total unseen messages
        foreach ($users as $user) {
            $totalUnseenMessages += Chatify::countUnseenMessages($user->id);
        }

        return response()->json([
            'total' => $totalUnseenMessages,
        ], 200);

    }


    /**
     * Put a user in the favorites list
     *
     * @param Request $request
     * @return void
     */
    public function favorite(Request $request)
    {
        $userId = $request['user_id'];
        // check action [star/unstar]
        $favoriteStatus = Chatify::inFavorite($userId) ? 0 : 1;
        Chatify::makeInFavorite($userId, $favoriteStatus);

        // send the response
        return Response::json([
            'status' => @$favoriteStatus,
        ], 200);
    }

    /**
     * Get favorites list
     *
     * @param Request $request
     * @return void
     */
    public function getFavorites(Request $request)
    {
        if(auth('sanctum')->check()) {
            $favorites = Favorite::where('user_id', Auth::user()->id)->get();
            $total = 0;
            $favoriteUsers = null;
            $status = 'success';
            $message = 'No data';
            if( count($favorites) > 0 ) {
                foreach ($favorites as $favorite) {
                    $favorite->user = User::where('id', $favorite->favorite_id)->first();
                }
                $favoriteUsers = $favorites;
                $total = count($favorites);
                $message = 'Data found';
            }

            return Response::json([
                'status' => $status,
                'message' => $message,
                'total' => $total,
                'favorites' => $favoriteUsers,
            ], 200);

        } else {
            return response()->json([
                'status' => 'success',
                'message' => 'User is not logged in',
                'total' => null,
                'favorites' => null,
            ]);
        }
    }

    /**
     * Search in messenger
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        if(auth('sanctum')->check()) {
            $input = trim(filter_var($request['input']));
            // $records = User::where('id','!=',Auth::user()->id)
            //     // ->where('name', 'LIKE', "%{$input}%")
            // ->Where(function ($query) use ($input) {
            //     $query->orWhere('users.first_name', 'LIKE', "%{$input}%")
            //     ->orWhere('users.last_name', 'LIKE', "%{$input}%")
            //     ->orWhere('users.email', 'LIKE', "%{$input}%");
            //     // ->Where('messages.receiver_id', '<>', auth()->id());
            // })
            // ->paginate($request->per_page ?? $this->perPage);
            $records = Message::select('users.*')
            ->leftjoin('users', 'ch_messages.to_id', '=', 'users.id')
            ->where(function ($query) {
                $query->where('ch_messages.from_id', Auth::user()->id)->orWhere('ch_messages.to_id', Auth::user()->id);
            })
            ->Where(function ($query) use ($input) {
                $query->orWhere('users.first_name', 'LIKE', "%{$input}%")
                ->orWhere('users.last_name', 'LIKE', "%{$input}%")
                ->orWhere('users.email', 'LIKE', "%{$input}%");
            })
            ->where('users.id','!=',Auth::user()->id)
            ->where(function ($query) {
                $query->whereNull('ch_messages.deleted_by')
                ->orWhereJsonDoesntContain('ch_messages.deleted_by', Auth::user()->id);
            })
            ->groupBy('users.id')
            ->paginate($request->per_page ?? $this->perPage);
            // foreach ($records->items() as $index => $record) {
            //     dd($record);
            //     $records[$index] += Chatify::getUserWithAvatar($record);
            // }

            return response()->json([
                'status' => 'success',
                'message' => 'data found',
                'records' => $records->items(),
                'total' => $records->total(),
                'last_page' => $records->lastPage()
            ], 200);
        } else {
            return response()->json([
                'status' => 'success',
                'message' => 'User is not logged in',
                'records' => null,
                'total' => null,
                'last_page' => null,
            ]);
        }
    }

    /**
     * Get shared photos
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sharedPhotos(Request $request)
    {
        $images = Chatify::getSharedPhotos($request['user_id']);

        foreach ($images as $image) {
            $image = asset(config('chatify.attachments.folder') . $image);
        }
        // send the response
        return Response::json([
            'shared' => $images ?? [],
        ], 200);
    }

    /**
     * Delete conversation
     *
     * @param Request $request
     * @return void
     */
    public function deleteConversation(Request $request)
    {
        // delete
        if(auth('sanctum')->check()) {

            // $delete = Chatify::deleteConversation($request['id']);

            $records = Message::where(function ($query) use($request) {
                $query->where('ch_messages.from_id', Auth::user()->id)->where('ch_messages.to_id', $request['id']);
            })
            ->orWhere(function ($query1) use($request) {
                $query1->where('ch_messages.from_id', $request['id'])->where('ch_messages.to_id', Auth::user()->id);
            })
            ->get();
            $delete = array();
            foreach ($records as $key => $message) {
                $deleteMessage = Message::where('id', $message->id)->first();
                $deleteMessage->deleted_by = json_encode(array(Auth::user()->id));
                if($deleteMessage->save()) {
                    $delete[] = $key;
                }
            }

            // send the response
            return Response::json([
                'deleted' => !empty($delete) || $delete ? 1 : 0,
            ], 200);
        }  else {
            return response()->json([
                'deleted' => 0,
            ]);
        }
    }

    /**
     * Delete message
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteMessage(Request $request)
    {
        // delete
        if(auth('sanctum')->check()) {
            $delete = Chatify::deleteMessage($request['id']);

            // send the response
            return Response::json([
                'deleted' => $delete ? 1 : 0,
            ], 200);
        } else {
            return response()->json([
                'deleted' => 0,
            ]);
        }
    }

    public function updateSettings(Request $request)
    {
        $msg = null;
        $error = $success = 0;

        // dark mode
        if ($request['dark_mode']) {
            $request['dark_mode'] == "dark"
                ? User::where('id', Auth::user()->id)->update(['dark_mode' => 1])  // Make Dark
                : User::where('id', Auth::user()->id)->update(['dark_mode' => 0]); // Make Light
        }

        // If messenger color selected
        if ($request['messengerColor']) {
            $messenger_color = trim(filter_var($request['messengerColor']));
            User::where('id', Auth::user()->id)
                ->update(['messenger_color' => $messenger_color]);
        }
        // if there is a [file]
        if ($request->hasFile('avatar')) {
            // allowed extensions
            $allowed_images = Chatify::getAllowedImages();

            $file = $request->file('avatar');
            // check file size
            if ($file->getSize() < Chatify::getMaxUploadSize()) {
                if (in_array(strtolower($file->extension()), $allowed_images)) {
                    // delete the older one
                    if (Auth::user()->avatar != config('chatify.user_avatar.default')) {
                        $path = Chatify::getUserAvatarUrl(Auth::user()->avatar);
                        if (Chatify::storage()->exists($path)) {
                            Chatify::storage()->delete($path);
                        }
                    }
                    // upload
                    $avatar = Str::uuid() . "." . $file->extension();
                    $update = User::where('id', Auth::user()->id)->update(['avatar' => $avatar]);
                    $file->storeAs(config('chatify.user_avatar.folder'), $avatar, config('chatify.storage_disk_name'));
                    $success = $update ? 1 : 0;
                } else {
                    $msg = "File extension not allowed!";
                    $error = 1;
                }
            } else {
                $msg = "File size you are trying to upload is too large!";
                $error = 1;
            }
        }

        // send the response
        return Response::json([
            'status' => $success ? 1 : 0,
            'error' => $error ? 1 : 0,
            'message' => $error ? $msg : 0,
        ], 200);
    }

    /**
     * Set user's active status
     *
     * @param Request $request
     * @return void
     */
    public function setActiveStatus(Request $request)
    {
        // dd(Auth::user()->id);
        if(auth('sanctum')->check()) {
            $activeStatus = $request['status'] > 0 ? 1 : 0;
            $status = User::where('id', Auth::user()->id)->update(['active_status' => $activeStatus]);
            return Response::json([
                'status' => $status,
                'message' => 'state updated',
            ], 200);
        } else {
            return response()->json([
                'status' => 'success',
                'message' => 'User is not logged in',
                'favorite' => null,
            ]);
        }
    }
}
