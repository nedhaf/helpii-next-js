<?php

namespace App\Http\Controllers\Api\Frontend\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ResetsPasswords;
use App\Repositories\Frontend\Auth\UserRepository;
use App\Http\Requests\Frontend\Auth\ResetPasswordRequest;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Models\Auth\User;

class ResetPasswordApiController extends Controller
{
    use ResetsPasswords;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * ChangePasswordController constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function reset(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'token' => 'required',
            // 'email' => 'required|email|exists:users',
            'password' => 'required|confirmed'
        ]);

        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()]);
        }

        $user = $this->userRepository->findByPasswordResetToken($request->token);
        if ($user && app()->make('auth.password.broker')->tokenExists($user, $request->token)) {
            try {

                $status = Password::reset(
                    $request->only('email', 'password', 'password_confirmation', 'token'),
                    function (User $user, string $password) {
                        $user->forceFill([
                            'password' => Hash::make($password)
                        ])->setRememberToken(Str::random(60));

                        $user->save();

                        event(new PasswordReset($user));
                    }
                );
                return $status == Password::PASSWORD_RESET
                ? response()->json(['message' => 'Password changed successfully.', 'status' => 200], 201)
                : response()->json(['message' => 'Unable change password', 'status' => 401], 401);
            } catch(Exception $e) {

                return response()->json(['errors' => $e->getMessage()]);
            }
        } else {
            return response()->json(['errors' => __('exceptions.frontend.auth.password.reset_problem')]);
        }
    }
}
