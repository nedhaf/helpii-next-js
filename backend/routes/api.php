<?php
// header('Access-Control-Allow-Origin: *');
// header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
// header('Access-Control-Allow-Headers: Content-Type, X-Auth-Token, Origin, Authorization');


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Frontend\GeneralController;
use App\Http\Controllers\Frontend\SearchController;
use App\Http\Controllers\Frontend\UserdetailsApiController;
use App\Http\Controllers\Frontend\SpSkillController;
use App\Http\Controllers\Frontend\UserAdsController;
use App\Http\Controllers\Frontend\CommonApiController;
use App\Http\Controllers\Frontend\SpavailabilityApiController;
use App\Http\Controllers\Frontend\ProfileApiController;
use App\Http\Controllers\Api\Frontend\User\FeedbackApiController;
use App\Http\Controllers\Api\Frontend\User\MessageApiController;
use App\Http\Controllers\vendor\Chatify\Api\MessagesController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('login', [AuthenticatedSessionController::class, 'frontLogin'])->middleware('guest')->name('login_frontend');
Route::get('/users', function (Request $request) {
    $tokenId = session('user_api_token_id', null);
    return response()->json([
            'token' => $tokenId,
        ]);
});
Route::group(['middleware' => ['auth:sanctum'] ], function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

    Route::get('/user', function (Request $request) {
        // echo "<pre>";
        // $user = $request->user();
        $user = Auth::user()->load(['Profile', 'favouriteusers']);
        $tokenName="web_api_token";
        $tokenId = session('user_api_token_id', null);


         $messagesController = new MessagesController();


        return response()->json([
            'user' => $user,
            // 'token' => $user->tokens()->where('personal_access_tokens.name', $tokenName)->first(),
            'token' => $tokenId,
            'chatify_counter' => $messagesController->getTotalUnseenMessages(),
        ]);
        // return $request->user();
    });

    // Route::get('user', [AuthenticatedSessionController::class, 'getAuthUser']);

    // User Settings
    Route::post('user-language-update',[UserdetailsApiController::class,'updateLanguage'])->name('update_user_language');
    Route::post('user-details-update',[UserdetailsApiController::class,'updateDetails'])->name('update_user_details');
    Route::post('user-currency-update',[UserdetailsApiController::class,'updateCurrency'])->name('update_user_currency');
    Route::post('availability-create-update', [SpavailabilityApiController::class, 'store'])->name('availability_create_update');
    Route::post('update-user-badges', [ProfileApiController::class, 'updateUserBadges'])->name('update_user_badges');
    Route::post('update-aboutme', [ProfileApiController::class, 'updateaboutMe'])->name('update_aboutme');
    Route::post('upload-avtar',[ProfileApiController::class,'uploadAvatar'])->name('upload_avtar');
    Route::post('get-fav-users', [ProfileApiController::class, 'getFavUsers'])->name('get_fav_users');

    // Sp-Skills API
    Route::post('create-spskills',[SpSkillController::class,'store'])->name('create_spskills');
    Route::post('edit-spskills', [SpSkillController::class, 'edit'])->name('edit_spskills');
    Route::post('update-spskills', [SpSkillController::class, 'update'])->name('update_spskills');
    Route::post('delete-spskills', [SpSkillController::class, 'destroy'])->name('delete_spskills');

    // User Ads
    Route::post('create-ad',[UserAdsController::class,'store'])->name('create_ad');
    Route::post('edit-ad', [UserAdsController::class, 'edit'])->name('edit_ad');
    Route::post('update-ad', [UserAdsController::class, 'update'])->name('update_ad');
    Route::post('delete-ad', [UserAdsController::class, 'destroy'])->name('delete_ad');

    // User Feedback
    Route::post('feedback-create',[FeedbackApiController::class,'store'])->name('create_feedback_app');
    Route::post('feedback-update',[FeedbackApiController::class,'update'])->name('update_feedback_app');
    Route::post('get-user-feedbacks',[FeedbackApiController::class,'getUserFeedbacks'])->name('get_user_feedback');

    // Fav users
    Route::post('get-fav-users', [ProfileApiController::class, 'getFavUsers'])->name('get_fav_users_app');
    Route::post('add-to-fav', [ProfileApiController::class, 'AddToFav'])->name('add_to_fav_app');
});

Route::get('get-skills', [GeneralController::class, 'getSkills'])->name('frontAllSkills');
Route::get('get-badges', [GeneralController::class, 'getBadges'])->name('frontAllBadge');
Route::post('search-users', [SearchController::class, 'searchsp'])->name('search_users');
Route::post('profile-details',[UserdetailsApiController::class,'index'])->name('profile_details_app');
Route::post('get-user-details/{slug}',[UserdetailsApiController::class,'userDetails'])->name('user_details_app');
Route::get('get-currency', [CommonApiController::class, 'getCurrency'])->name('get_currency_app');
Route::post('get-currency-by-id', [CommonApiController::class, 'getCurrencyById'])->name('get_currencyby_id_app');
Route::post('get-user-availability', [SpavailabilityApiController::class, 'getAvailability'])->name('get_user_availability');
// Route::get('get-profile-details',[UserdetailsApiController::class,'profileDetails'])->name('get_profile_details_app');


Route::group(['prefix' => 'chat', 'middleware' => ['auth:sanctum']], function() {

/*    Route::post('user-msg-inbox', [MessageApiController::class,'inbox'])->name('usr_inbox_app');
    Route::post('get-user-chats', [MessageApiController::class,'fetchChat']);
    Route::post('searchuserchat', [MessageApiController::class,'SearchChatUsers']);
    Route::post('get-chat-user-detail', [MessageApiController::class,'getChatUserDetails']);
    Route::post('send-user-message', [MessageApiController::class,'SendUserMessage']);
    Route::post('delete-user-message', [MessageApiController::class,'destroyMessage']);
*/

    /**
     * Authentication for pusher private channels
    */
    Route::post('/chat-auth', [MessagesController::class, 'pusherAuth'])->name('app_chatify_usr_auth');
    /**
    *  Fetch info for specific id [user/group]
    */
    Route::post('idInfo', [MessagesController::class, 'idFetchData'])->name('app_chatify_usr_inbox_app');
    /**
    * Send message route
    */
    Route::post('sendMessage', [MessagesController::class, 'send'])->name('app_chatify_usr_send_msg');
    /**
    * Fetch messages
    */
    Route::post('fetchMessages', [MessagesController::class, 'fetch'])->name('app_chatify_usr_fetch_msg');
    /**
    * Make messages as seen
    */
    Route::post('makeSeen', [MessagesController::class, 'seen'])->name('app_chatify_usr_seen_msg');
    /**
    * Get contacts
    */
    Route::get('get-Contacts', [MessagesController::class, 'getContacts'])->name('app_chatify_contacts_get');
    /**
    * get favorites list
    */
    Route::post('favorites', [MessagesController::class, 'getFavorites'])->name('app_chatify_favorites');
    /**
    * Search in messenger
    */
    Route::get('search', [MessagesController::class, 'search'])->name('app_chatify_search_user');
    /**
    * Delete Conversation
    */
    Route::post('deleteConversation', [MessagesController::class, 'deleteConversation'])->name('app_chatify_delete_conversation');
    /**
    * Delete Message
    */
    Route::post('deleteMessage', [MessagesController::class, 'deleteMessage'])->name('app_chatify_delete_message');
    /**
    * Set active status
    */
    Route::post('setActiveStatus', [MessagesController::class, 'setActiveStatus'])->name('app_chatify_activeStatus');

    /**
     * Star in favorite list
     */
    Route::post('star', [MessagesController::class, 'favorite'])->name('api.star');


    /**
     * Get shared photos
     */
    Route::post('/shared', [MessagesController::class, 'sharedPhotos'])->name('app_chatify_shared');

    Route::get('/chatifyUnreadMsgCounter', [MessagesController::class, 'getTotalUnseenMessagesApi'])->name('getTotalUnseenMessages');

});
