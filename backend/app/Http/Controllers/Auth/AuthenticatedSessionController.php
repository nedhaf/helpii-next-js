<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Auth\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth AS LaraAuthApp;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): Response
    {
        $request->authenticate();

        LaraAuthApp::user()->session()->regenerate();

        return response()->noContent();
        // return redirect()->intended(url('/'));
    }

    public function frontLogin(LoginRequest $request)
    {
       $userData = User::where('email','=',$request->email)->first();
        $data = (object) [];
        if(!isset($userData->id)){
            return response()->json([
                "status" => 401,
                "message" => "Invalid Username or Password",
                'data' => $data
            ]);
        }else {
            $checkPass = Hash::check($request->password, $userData->password);
            if($checkPass){
                if(strtolower($userData->role) ==="admin")
                {
                    return response()->json([
                        'status' => 401,
                        'message' => 'You are not Authorized this Login',
                        'data' => $data,
                    ]);
                }
            }
        }
        $remember_me = $request->has('remember') ? true : false;
        if (!(Auth::attempt(['email' => $request->email, 'password' => $request->password ],$remember_me )))
        {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return response()->json(['status'=>401,'message'=>"Wrong emailid or password!"]);
        }
        //LogActivity::addToLog('User Login');
        //$updateuser = User::where('email','=',$request->email)->update(["active" => true ]);
        $token = Auth::user()->createToken('web_api_token')->plainTextToken;
        session(['user_api_token_id' => $token]);
        $request->session()->regenerate();
        return response()->json(['user' => Auth::user(), 'token' => $token,'status'=>200, 'message'=> 'Login Successful']);

    }

    public function getAuthUser()
    {
       return LaraAuthApp::user();
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->user()->tokens()->delete();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
