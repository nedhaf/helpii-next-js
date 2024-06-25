<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Advertisements;
use App\Models\Skill;
use App\Models\Currency;
use Illuminate\Support\Facades\File;

class AdvertisementApiController extends Controller
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
        if(auth('sanctum')->check()) {
            $getDefaultAdvertise = Advertisements::with('getSkill')->where('isFront', 1)->get();
            $getads = Advertisements::with('getSkill')->latest()->groupBy('position')->get();
            $currency = Currency::where('id', 2)->first();
            $responseData = [];
            foreach ($getads as $position => $ads) {
                $responseData[] = $ads->toArray();
            }

            return response()->json(
            [
                'errors' => '',
                "status" => 200,
                "message" => $responseData,
            ],200);
        } else {
            return response()->json(['errors' => 'User is not logged in'],401);
        }
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
    public function destroy($id)
    {
        //
    }
}
