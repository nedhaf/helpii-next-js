<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

use App\Models\Auth\User;
use App\Models\Spavailability;

class SpavailabilityApiController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $uid = Auth::user()->id;
        $Spavailability = Spavailability::where('user_id',$uid)->first();
        if(empty($Spavailability)) {
            $Spavailability = new Spavailability();
            $Spavailability->user_id = $uid;
        }

        // $mon = $request->mon;
        // $tue = $request->tue;
        // $wed = $request->wed;
        // $thu = $request->thu;
        // $fri = $request->fri;
        // $sat = $request->sat;
        // $sun = $request->sun;

        $timeslot=array();
        $timeslot["monday"] = $request->monday;
        $timeslot["tuesday"] = $request->tuesday;
        $timeslot["wednesday"] = $request->wednesday;
        $timeslot["thursday"] = $request->thursday;
        $timeslot["friday"] = $request->friday;
        $timeslot["saturday"] = $request->saturday;
        $timeslot["sunday"] = $request->sunday;

        $Spavailability->timeslot = json_encode($timeslot);

        if( $Spavailability->save() ) {
            $message = __('Availability updated successfully!');
            $errors = null;
        } else {
            $message = __('Availability not updated successfully!');
            $errors = null;
        }

        return response()->json(
        [
            "status" => 200,
            'errors' => $errors,
            "message" => $message,
        ],200);
    }

    public function getAvailability(Request $request)
    {
        // $uid = Auth::user()->id;
        $uid = $request->uid;
        $Spavailability = Spavailability::where('user_id',$uid)->first();
        if( !empty($Spavailability) ){
            $message = __('Availability found successfully!');
            $errors = null;
            $timeSlots = json_decode($Spavailability->timeslot, true);
        } else {
            $message = __('Availability not found successfully!');
            $errors = null;
            $timeSlots = null;
        }
        return response()->json(
        [
            "status" => 200,
            'errors' => $errors,
            "message" => $message,
            "timeslots" => $timeSlots
        ],200);
    }
}
