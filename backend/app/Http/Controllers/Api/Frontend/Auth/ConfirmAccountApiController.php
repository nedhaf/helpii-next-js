<?php

namespace App\Http\Controllers\Api\Frontend\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\Frontend\Auth\UserConfirmed;
use App\Repositories\Frontend\Auth\UserRepository;
use App\Notifications\Frontend\Auth\UserNeedsConfirmation;
use App\Models\Auth\User;
use Illuminate\Support\Facades\Auth;

class ConfirmAccountApiController extends Controller
{
    //

    /**
     * @var UserRepository
     */
    protected $user;

    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }

    /**
     * @param $token
     *
     * @return mixed
     * @throws \App\Exceptions\GeneralException
     */
    public function confirm($token)
    {
        $user = User::where('confirmation_code', $token)->first();

        if( $user->confirmed == 1 ) {
            return response()->json([
                "status" => 200,
                "message" => "You have already confirmed this account.",
            ]);
        }

        if( $user->confirmation_code == $token ) {

            $user->confirmed = 1;
            event(new UserConfirmed($user));
            $user->save();
            // if (Auth::attempt(['email' => $user->email, 'password' => $user->password])) {
            //     $useSignup = Auth::user();
            //     $mobile_confirm_ac_token = $useSignup->createToken('confirm_ac_mobile_api_token')->plainTextToken;
            //     return response()->json([
            //         'status'=>200,
            //         'message'=> 'Login Successful',
            //         'token' => $mobile_confirm_ac_token,
            //         'data' => [
            //             'user_data' => $useSignup,
            //         ],
            //     ]);
            // }
                return response()->json([
                    'status'=>200,
                    'message'=> 'Confirmed',
                ]);
        } else {
            return response()->json([
                "status" => 200,
                "message" => "You your confirmation code does not match",
            ]);
        }

    }
}
