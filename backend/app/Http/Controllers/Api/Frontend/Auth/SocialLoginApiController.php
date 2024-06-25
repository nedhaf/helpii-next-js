<?php

namespace App\Http\Controllers\Api\Frontend\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exceptions\GeneralException;
use GeneaLabs\LaravelSocialiter\Facades\Socialiter;
use Laravel\Socialite\Facades\Socialite;
use App\Events\Frontend\Auth\UserLoggedIn;
use App\Models\Auth\User;
use App\Repositories\Frontend\Auth\UserRepository;
use App\Helpers\Frontend\Auth\Socialite as SocialiteHelper;
use Illuminate\Support\Facades\Auth AS LaraAuthApp;
use App\Models\Auth\SocialAccount;
use App\Models\Spavailability;


class SocialLoginApiController extends Controller
{
    //
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var SocialiteHelper
     */
    protected $socialiteHelper;

    /**
     * SocialLoginController constructor.
     *
     * @param UserRepository  $userRepository
     * @param SocialiteHelper $socialiteHelper
     */
    public function __construct(UserRepository $userRepository, SocialiteHelper $socialiteHelper)
    {
        $this->userRepository = $userRepository;
        $this->socialiteHelper = $socialiteHelper;
    }

    /**
     * @param Request $request
     * @param $provider
     *
     * @throws GeneralException
     *
     * @return \Illuminate\Http\RedirectResponse|mixed
     */

    public function login(Request $request)
    {
        $provider = $request->provider;
        $token = $request->access_token;


        $scopes = empty(config("services.{$provider}.scopes")) ? false : config("services.{$provider}.scopes");
        $with = empty(config("services.{$provider}.with")) ? false : config("services.{$provider}.with");
        $fields = empty(config("services.{$provider}.fields")) ? false : config("services.{$provider}.fields");

        // If the provider is not an acceptable third party than kick back
        if (! in_array($provider, $this->socialiteHelper->getAcceptedProviders())) {
            // return redirect()->route(home_route())->withFlashDanger(__('auth.socialite.unacceptable', ['provider' => $provider]));
            return response()->json(['errors' => ':provider is not an acceptable login type.'], 422);
        }

        if ($provider === 'apple') { //For apple login

            //--------------------------------------------------
            try {

                $acceptedProviders = $this->socialiteHelper->getAcceptedProviders();

                $providerUser = Socialite::driver('sign-in-with-apple')->scopes(["name", "email"])->stateless()->getAccessTokenResponse($token);
                // dd($providerUser);
                $decodedIdToken = json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $providerUser['id_token'])[1]))));
                //$decodedIdToken = JWT::decode($idToken, null, false); // Replace `null` with your public key if needed
                // dd( $decodedIdToken);

                if (!isset($providerUser['id_token'])) {
                    return response()->json(['errors' => 'Failed to retrieve Apple ID token.'], 422);
                }
                $idToken = $providerUser['id_token'];
                $providerUser['decode_data'] = $decodedIdToken;
                $providerUser['other_data'] = $request->all();
            } catch (\Exception $e) {
                // Log or handle the error
                \Log::error($e);
                return response()->json(['errors' => $e->getMessage()], 500);
            }
        } else {
            $providerUser = Socialite::driver($provider)->userFromToken($token);
        }

        // check if access token exists or not
        // $user = $this->userRepository->findOrCreateProvider($this->getProviderUser($provider), $provider);
        // $user = User::where('email', $providerUser->email)->firstOrFail();
        // dd($providerUser);
        $user = $this->userRepository->findOrCreateProvider($providerUser, $provider);
        if(empty($user->slug)){
            $checkUser = User::where('email',$user->email)->get();
            $userData = $checkUser[0];
            $slug = strtolower($user->first_name."-".$user->last_name);
            $slug = str_replace(" ", "-", $slug);

            $count = User::where('slug', 'like', '%'.$slug.'%')->count();
            if($count > 0){
                $slug = $slug."-".($count+1);
            }
            $loggedUser = User::find($userData->id);
            $loggedUser->slug = $slug;
            $loggedUser->save();
        }

        if (is_null($user)) {
            // return redirect()->route(home_route())->withFlashDanger(__('exceptions.frontend.auth.unknown'));
            return response()->json(['errors' => __('exceptions.frontend.auth.unknown')], 422);
        }

        // Check to see if they are active.
        if (! $user->isActive()) {
            // throw new GeneralException(__('exceptions.frontend.auth.deactivated'));
            return response()->json(['errors' => __('exceptions.frontend.auth.deactivated')], 422);
        }

        // Account approval is on
        if ($user->isPending()) {
            // throw new GeneralException(__('exceptions.frontend.auth.confirmation.pending'));
            return response()->json(['errors' => __('exceptions.frontend.auth.confirmation.pending')], 422);
        }

        $token = $user->createToken('social_login_mobile_api_token')->plainTextToken;

        $getTimeSlot = Spavailability::where('user_id', $user->id)->get();

        if( empty($getTimeSlot) ) {
            Spavailability::create([
                'user_id' => $user->id,
                'timeslot' => "{\"monday\":{\"close\":1},\"tuesday\":{\"close\":1},\"wednesday\":{\"close\":1},\"thursday\":{\"close\":1},\"friday\":{\"close\":1},\"saturday\":{\"close\":1},\"sunday\":{\"close\":1}}"
            ]);
        }

        return response()->json(
        [
            'errors' => '',
            "status" => 200,
            "message" => "Success",
            'token' => $token,
            'data' => [
                'user_data' => $user,
            ],
        ],200);
        // dd($user);
    }

    /**
     * TO check incomming user is already exists with apple device or not
     * @param Request $request
     * @param $provider
     *
     * @throws GeneralException
     *
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function checkUser(Request $request)
    {
        $appleUserId = $request->apple_sub;
        try{
            $checkAppleUser = SocialAccount::where('provider_id', $appleUserId)->first();
        } catch( Exception $e ) {
            $e->getMessage();
        }

        if( !empty($checkAppleUser) ) {
            $isExists = true;
            $Userdata = User::select('id','first_name','last_name','email')->where('id', $checkAppleUser->user_id)->first();
            return response()->json(
            [
                "errors" => '',
                "message" => "Success",
                "results" => $isExists,
                "user_id" => $Userdata->id,
                "first_name" => $Userdata->first_name,
                "last_name" => $Userdata->last_name,
                "email" => $Userdata->email,
            ],200);
        } else {
            $isExists = false;
            return response()->json(
            [
                "errors" => '',
                "message" => "Success",
                "results" => $isExists,
                "user_id" => "",
                "first_name" => "",
                "last_name" => "",
                "email" => "",
            ],200);
        }

    }
    // Login with aple - testing
    // public function login(Request $request)
    // {
    //     $provider = $request->provider;
    //     $token = $request->access_token;

    //     $acceptedProviders = $this->socialiteHelper->getAcceptedProviders();

    //     if (!in_array($provider, $acceptedProviders)) {
    //         return response()->json(['errors' => "{$provider} is not an acceptable login type."], 422);
    //     }

    //     if ($provider === 'apple') {
    //         $appleCredentials = Socialite::driver('sign-in-with-apple')->stateless()->getAccessTokenResponse($token);
    //         dd($appleCredentials);
    //         if (!isset($appleCredentials['id_token'])) {
    //             return response()->json(['errors' => 'Failed to retrieve Apple ID token.'], 422);
    //         }

    //         $idToken = $appleCredentials['id_token'];

    //         // Validate the ID token and extract user information
    //         // You need to implement the logic for ID token validation and user information extraction here

    //         $user = $this->userRepository->findOrCreateProvider($providerUser, $provider);

    //         $token = $user->createToken('social_login_mobile_api_token')->plainTextToken;

    //         return response()->json([
    //             'errors' => '',
    //             'status' => 200,
    //             'message' => 'Success',
    //             'token' => $token,
    //             'data' => [
    //                 'user_data' => $user,
    //             ],
    //         ], 200);
    //     }

    //     $providerUser = Socialite::driver($provider)->userFromToken($token);

    //     $user = $this->userRepository->findOrCreateProvider($providerUser, $provider);

    //     if (empty($user->slug)) {
    //         $checkUser = User::where('email', $user->email)->get();
    //         $userData = $checkUser[0];
    //         $slug = strtolower($user->first_name . "-" . $user->last_name);
    //         $slug = str_replace(" ", "-", $slug);

    //         $count = User::where('slug', 'like', '%' . $slug . '%')->count();
    //         if ($count > 0) {
    //             $slug = $slug . "-" . ($count + 1);
    //         }

    //         $loggedUser = User::find($userData->id);
    //         $loggedUser->slug = $slug;
    //         $loggedUser->save();
    //     }

    //     if (is_null($user)) {
    //         return response()->json(['errors' => __('exceptions.frontend.auth.unknown')], 422);
    //     }

    //     if (!$user->isActive()) {
    //         return response()->json(['errors' => __('exceptions.frontend.auth.deactivated')], 422);
    //     }

    //     if ($user->isPending()) {
    //         return response()->json(['errors' => __('exceptions.frontend.auth.confirmation.pending')], 422);
    //     }

    //     $token = $user->createToken('social_login_mobile_api_token')->plainTextToken;

    //     return response()->json([
    //         'errors' => '',
    //         'status' => 200,
    //         'message' => 'Success',
    //         'token' => $token,
    //         'data' => [
    //             'user_data' => $user,
    //         ],
    //     ], 200);
    // }

    public function checkJwtStructure($token) {
        // Split the JWT into its components
        $jwtParts = explode('.', $token);

        if (count($jwtParts) != 3) {
            return false;
        }

        // Decode the components
        $header = json_decode(base64_decode($jwtParts[0]), true);
        $payload = json_decode(base64_decode($jwtParts[1]), true);

        // Inspect the decoded components
        echo "Header:\n";
        print_r($header);
        echo "\n";

        echo "Payload:\n";
        print_r($payload);
        echo "\n";

        return true;
    }
    /**
     * @param  $provider
     *
     * @return mixed
     */
    protected function getAuthorizationFirst($provider)
    {
        $socialite = Socialite::driver($provider);
        $scopes = empty(config("services.{$provider}.scopes")) ? false : config("services.{$provider}.scopes");
        $with = empty(config("services.{$provider}.with")) ? false : config("services.{$provider}.with");
        $fields = empty(config("services.{$provider}.fields")) ? false : config("services.{$provider}.fields");

        if ($scopes) {
            $socialite->scopes($scopes);
        }

        if ($with) {
            $socialite->with($with);
        }

        if ($fields) {
            $socialite->fields($fields);
        }
        dd($socialite->redirect());
        return $socialite->redirect();
    }

    /**
     * @param $provider
     *
     * @return mixed
     */
        protected function getProviderUser($provider)
        {
            return Socialite::driver($provider)->user();
        }
}
