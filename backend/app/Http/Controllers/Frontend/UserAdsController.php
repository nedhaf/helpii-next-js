<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Skill;
use App\Models\Spskill;
use App\Models\UserAds;
use App\Models\Auth\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class UserAdsController extends Controller
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
        $UserAds = new UserAds();
        $uid = Auth::user()->id;

        $UserAds->user_id           = $uid;
        $UserAds->skill_id          = $request->skill_id;
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
        $UserAds->status            = 1;

        if($UserAds->save()) {
            $message = __('Ad created successfully.');
            $errors = null;
            // Send notification for users
        } else {
            $message = null;
            $errors = __('Oops! Ad not created!');
        }

        return response()->json(
        [
            'errors' => $errors,
            "status" => 200,
            "message" => $message,
        ],200);
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
    public function edit(Request $request)
    {
        //

        $getEditAds = UserAds::where('id', $request->ads_id)->where('user_id',$request->uid)->first();

        if($getEditAds) {
            $message = __('Ads found successfully!');
            $errors = '';
            $adsdata = $getEditAds;
        } else {
            $message = '';
            $errors = __('Ads not found!');
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
     */
    public function update(Request $request)
    {
        //
        $uid = Auth::user()->id;
        $adsId = $request->adsId;

        $UserAds = UserAds::where('id',$adsId)->where('user_id',$uid)->first();

        $UserAds->skill_id          = $request->skill_id;
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
        $UserAds->status            = 1;

        if($UserAds->save()) {
            $message = __('Ad updated successfully.');
            $errors = null;
            // Send notification for users
        } else {
            $message = null;
            $errors = __('Oops! Ad not updated!');
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
     */
    public function destroy(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'ads_id' => 'required|exists:user_ads,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()->get('skill_id')], 422);
        }

        $uid = Auth::user()->id;

        $UserAds = UserAds::where('id',$request->ads_id)->where('user_id',$uid)->firstOrFail();

        if($UserAds->delete()) {
            $message = __('Ads deleted successfully!');
            $errors = null;
        } else {
            $message = null;
            $errors = __('Add not deleted!');
        }

        return response()->json(
        [
            'errors' => $errors,
            "status" => 200,
            "message" => $message,
        ],200);
    }
}
