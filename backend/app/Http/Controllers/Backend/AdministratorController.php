<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use DateTime;
use App\Models\Auth\User;
use App\Http\Requests\Auth\LoginRequest;

class AdministratorController extends Controller
{
    //

    public function login()
    {
        if( Auth::check() ){
            if( Auth::user()->roles[0]['id'] == 1 ) {
                return redirect()->route('administrator.backend_dashboard');
            }
        }

        return view('backend.authentications.login');
    }

    public function authentication(LoginRequest $request)
    {
        $request->authenticate();

        // get the username and password which has provided by the admin
        $credentials = $request->only('email', 'password');
        if (Auth::guard('web')->attempt($credentials)) {
            $authAdmin = Auth::guard('web')->user();
            $adminUser = $authAdmin->roles;
            if( $adminUser[0]['id'] == 1 ){
                return redirect()->route('administrator.backend_dashboard');
            } else {
                return redirect()->back()->with('alert', 'Oops, you do not have administrator permission!');
                Auth::guard('web')->logout();
            }
        } else {
            return redirect()->back()->with('alert', 'Oops, username or password does not match!');
        }

        return view('backend.authentications.login');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        // invalidate the admin's session
        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('administrator');
    }
}
