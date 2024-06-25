<?php

namespace App\Http\Controllers\Backend\Auth\UserManagement;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use App\Models\Auth\User;
use App\Repositories\Backend\Auth\UserRepository;
use App\Http\Requests\Backend\Auth\User\StoreUserRequest;
use App\Http\Requests\Backend\Auth\User\UpdateAccountRequest;
use App\Http\Requests\Backend\Auth\User\UpdateProfileRequest;

class UserManagementController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * UserController constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $getUsers = $this->userRepository->getActivePaginated('id', 'desc');

        $getSuspendedUsersCount = $this->userRepository->userCounts('suspended');
        $getActiveUsersCount = $this->userRepository->userCounts('active');
        $getInActiveUsersCount = $this->userRepository->userCounts('inactive');
        // dd($getActiveUsersCount);
        return view('backend.auth.user-management.users.index', compact( "getUsers", "getSuspendedUsersCount", "getActiveUsersCount", "getInActiveUsersCount" ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('backend.auth.user-management.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        //
        $userStore = $this->userRepository->create($request->all());

        return redirect()->route('administrator.backend_user_management');
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
    public function edit(string $id, User $user)
    {
        //
        // Render page based on tabs
        if( request()->routeIs('administrator.backend_edit_user') ) {
            $page = 'account';
        } elseif ( request()->routeIs('administrator.backend_edit_user_security') ) {
            $page = 'security';
        } elseif ( request()->routeIs('administrator.backend_edit_user_connection') ) {
            $page = 'connections';
        } elseif ( request()->routeIs('administrator.backend_edit_user_preferances') ) {
            $page = 'preferences';
        }

        $editUser = $this->userRepository->getByColumn('uuid', $id);

        return view('backend.auth.user-management.users.pages.'.$page, compact('editUser'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAccountRequest $request, string $id, User $user)
    {
        //
        if( request()->routeIs('administrator.backend_update_user_account') ) {
            $updateUserAccount = $this->userRepository->update($id, $request->all(), 'user-account', 'uuid');
            return redirect()->back()->with('success', 'User account updated');
        } elseif ( request()->routeIs('administrator.backend_update_user_security') ) {
            $updateUserAccount = $this->userRepository->update($id, $request->all(), 'user-security', 'uuid');
            return redirect()->back()->with('success', 'User security updated');
        }

    }

    /**
     * Update the user profile.
     */
    public function updateProfile(UpdateProfileRequest $request, string $id,)
    {
        $updateUserProfile = $this->userRepository->updateProfile($id, $request->all(), 'user-profile', 'uuid');

        if( $updateUserProfile ) {
            return redirect()->back()->with('success', 'User profile updated');
        }
    }

    public function userSuspendRise(Request $request, $id)
    {
        if( request()->routeIs('administrator.backend_suspend_user') ) {
            $updateUserSuspendRise = $this->userRepository->userSuspendOrRestore($id,'user-suspend', 'uuid');
            $status = "success";
            $msg = "User Suspended successfully.";
        } elseif ( request()->routeIs('administrator.backend_rise_user') ) {
            $updateUserSuspendRise = $this->userRepository->userSuspendOrRestore($id,'user-rise', 'uuid');
            $status = "success";
            $msg = "User Rise successfully.";
        }

        return redirect()->back()->with($status, $msg);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, User $deletedUser)
    {
        //
        $deleteUser = $this->userRepository->forceDelete($id);
        $status = "success";
        $msg = "User deleted successfully.";

        return redirect()->route('administrator.backend_user_management')->with($status, $msg);
    }

    /**
     * Import users with all data from live site
     */
    public function importUsers()
    {
        $endpoint = "https://www.helpii.se/api/get-all-users";
        $client = new Client();
        $guzzleResponse = $client->get($endpoint);
        if ($guzzleResponse->getStatusCode() == 200){
            $response = json_decode($guzzleResponse->getBody(),true);
            $userResults = $response['results'];
            foreach( $userResults as $ukey => $user ) {
                if( $user['id'] != 1 ) {
                    echo "<pre>"; print_r($user); echo "</pre>";
                    // $importingUser = $this->userRepository->importingUsers($user);
                }
            }
            die("called");
        }
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $endpoint);
        // curl_setopt($ch, CURLOPT_POST, 0);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // $response = curl_exec ($ch);
        // $err = curl_error($ch);  //if you need
        // curl_close ($ch);
        // $Users = json_decode($response, true);
        // foreach ($Users as $key => $users) {
        //     if( $key == 'results' ) {
        //         foreach( $users as $ukey => $user ) {
        //             if( $user['id'] != 1 ) {
        //                 $importingUser = $this->userRepository->importingUsers($users);
        //             }
        //         }
        //         die("called");
        //     }
        // }
    }
}
