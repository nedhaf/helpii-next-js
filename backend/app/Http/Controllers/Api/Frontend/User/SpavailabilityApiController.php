<?php

namespace App\Http\Controllers\Api\Frontend\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Spavailability;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class SpavailabilityApiController extends Controller
{
    //
    public function create(Request $request ) {

    }

    public function store(Request $request ) {
        if(auth('sanctum')->check()) {
            $user = auth()->id();
            $validation = Validator::make($request->all(), [
                'user_id'   => 'required|exists:users,id',
            ]);

            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()]);
            }

            if(!empty($request->user_id)){
                $Spavailability = Spavailability::where('user_id',$request->user_id)->first();
                if(empty($Spavailability)) {
                    $Spavailability = new Spavailability();
                }
            } else {
                $Spavailability = new Spavailability();
            }

            // dd($request->all());
            $timeslot=array();
            $timeslot["monday"] = $request->monday;
            $timeslot["tuesday"] = $request->tuesday;
            $timeslot["wednesday"] = $request->wednesday;
            $timeslot["thursday"] = $request->thursday;
            $timeslot["friday"] = $request->friday;
            $timeslot["saturday"] = $request->saturday;
            $timeslot["sunday"] = $request->sunday;

            $Spavailability->user_id = $user;
            $Spavailability->timeslot = json_encode($timeslot);

            $Spavailability->save();

            if(!empty($request->user_id)){
                return response()->json([
                    'success' => [
                        'message' => __('strings.new.availability_update_message')
                    ],
                    'errors' => []
                ]);
            }

            return response()->json([
                'success' => [
                    'message' => __('strings.new.availability_insert_message')
                ],
                'errors' => []
            ]);

        }  else {
            return response()->json(['errors' => 'User is not logged in'],401);
        }
    }
}
