<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

use App\Models\Feedback;
use App\Models\OverallProfileRating;
use App\Models\Auth\User;
use App\Models\Spskill;
use App\Models\Skill;
use App\Models\UserNotifications;

class FeedbackApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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

    /**
     * Get the All feedbacks of specific user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getUserFeedbacks(Request $request)
    {
        // if(auth('sanctum')->check()) {
        //     $userId = auth()->id();
        //     try {
        //         if( $request->filter_by == 'short_by_latest' ) {
        //             $getFeedbacks = Feedback::with('Skill')->leftjoin('users', 'users.id', '=', 'rating.to_userid')
        //             ->leftJoin('social_accounts', function($join){
        //                 $join->on('social_accounts.user_id', '=', 'rating.to_userid')
        //                 ->on('users.avatar_type', '=', 'social_accounts.provider');
        //             })
        //             ->select('rating.*','users.first_name','users.last_name','users.avatar_type','users.avatar_location','users.email','social_accounts.provider','social_accounts.avatar')
        //             ->where('rating.from_userid', $userId)
        //             ->orderBy('rating.id', 'desc')
        //             ->get();
        //         } elseif ( $request->filter_by == 'short_by_oldest' ) {
        //             $getFeedbacks = Feedback::with('Skill')->leftjoin('users', 'users.id', '=', 'rating.to_userid')
        //             ->leftJoin('social_accounts', function($join){
        //                 $join->on('social_accounts.user_id', '=', 'rating.to_userid')
        //                 ->on('users.avatar_type', '=', 'social_accounts.provider');
        //             })
        //             ->select('rating.*','users.first_name','users.last_name','users.avatar_type','users.avatar_location','users.email','social_accounts.provider','social_accounts.avatar')
        //             ->where('rating.from_userid', $userId)
        //             ->orderBy('rating.id', 'asc')
        //             ->get();
        //         }

        //         return response()->json([
        //             "status" => 200,
        //             "message" => "Success",
        //             "results" => $getFeedbacks
        //         ],200);
        //     } catch (\Exception $e) {
        //         // Log the error or handle it in a way that suits your application
        //         return response()->json(['errors' => $e->getMessage()],200);
        //     }
        // } else {
            return response()->json(['errors' => 'User is not logged in'],401);
        // }
    }
}
