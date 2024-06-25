<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Config;
use Illuminate\Support\Facades\Storage;
use App\Models\Skill;
use App\Models\Spskill;
use App\Models\Currency;
use App\Models\Feedback;
use App\Models\OverallProfileRating;
use App\Models\Auth\User;
use App\Models\Auth\SocialAccount;
use App\Models\UserAds;
use App\Models\Advertisements;
use App\Models\ProfileVisitors;

class CommonApiController extends Controller
{
    /**
     * Get recent ads for mobile app.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCurrency(Request $request)
    {
        $Currency = Currency::orderBy('id', 'desc')->latest()->get();

        if( $Currency ) {
            return response()->json(
            [
                "status" => 200,
                'errors' => null,
                "message" => "Curency data found.",
                'results' => $Currency
            ],200);
        } else {
            return response()->json(
            [
                "status" => 200,
                'errors' => null,
                "message" => "Curency data not found",
                'results' => null
            ],200);
        }
    }

    public function getCurrencyById(Request $request)
    {
        $Currency = Currency::where('id', $request->currency_id)->first();

        if( $Currency ) {
            return response()->json(
            [
                "status" => 200,
                'errors' => null,
                "message" => "Curency data found.",
                'results' => $Currency
            ],200);
        } else {
            return response()->json(
            [
                "status" => 200,
                'errors' => null,
                "message" => "Curency data not found",
                'results' => null
            ],200);
        }
    }
}
