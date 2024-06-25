<?php

namespace App\Http\Controllers\Api\Frontend\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Password;
use Carbon\Carbon;
// use App\Notifications\Frontend\Auth\UserNeedsPasswordReset;
use App\Models\Auth\User;

class ForgotPasswordApiController extends Controller
{
    use SendsPasswordResetEmails;

    public function sendPasswordResetLink(Request $request)
    {
        //
        $validation = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
        ]);

        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()]);
        }

        try{

            $response = $this->broker()->sendResetLink(
                $request->only('email')
            );

            return $response == Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Reset link sent to your email.', 'status' => 200, 'email' => $request->email], 201)
            : response()->json(['message' => 'Unable to send reset link', 'status' => 400], 401);
        } catch(Exception $e){
            return response()->json(['errors' => $e->getMessage()]);
        }
    }
}
