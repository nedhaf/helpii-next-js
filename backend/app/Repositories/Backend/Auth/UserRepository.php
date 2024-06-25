<?php

namespace App\Repositories\Backend\Auth;

use App\Models\Auth\User;
use App\Models\Profile;
use Illuminate\Support\Facades\DB;
use App\Exceptions\GeneralException;
use Illuminate\Support\Facades\Hash;
use App\Repositories\BaseRepository;
use \Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

/**
 * Class UserRepository.
 */
class UserRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return User::class;
    }

    public function ProfileModel()
    {
        return Profile::class;
    }

    /**
     * @return mixed
     */
    public function getUnconfirmedCount() : int
    {
        return $this->model->where('confirmed', 0)->count();
    }

    /**
     * @param string $orderBy
     * @param string $sort
     *
     * @return mixed
     */

    public function getActivePaginated($orderBy = 'created_at', $sort = 'desc', $searchbyfirstName ='', $searchbylastName = '', $searchbyEmail = '')
    {

        // $users = $this->model->with('roles')->whereHas("roles", function($q){ $q->whereNot("id", 1); })->active();
        $users = $this->model->with(['roles', 'profile'])->whereHas("roles", function($q){ $q->whereNot("id", 1); });

        if( !empty( $searchbyEmail ) ){
           $users = $users->where ('email','like','%'.$searchbyEmail.'%');
        }

        if( !empty( $searchbyfirstName ) ){
            $users = $users->where('first_name','like','%'.$searchbyfirstName.'%');
        }

        if( !empty( $searchbylastName ) ){
            $users = $users->where('last_name','like','%'.$searchbylastName.'%');
        }

        $users = $users->orderBy($orderBy, $sort)->withTrashed()->get();
        // $users = $users->paginate($paged);
        return $users;
    }

     /**
     * Get count of users
     *
     * @param string $countOf
     *
     * @return mixed $usersCount
     */
    public function userCounts( $countOf ) : int
    {
        $usersCount = $this->model->where('id', '!=', 1);

        if( $countOf == 'active' ) {
            $usersCount = $usersCount->where('active', 1)->withTrashed()->count();
        } elseif( $countOf == 'inactive' ) {
            $usersCount = $usersCount->where('active', 0)->withTrashed()->count();
        } elseif( $countOf == 'suspended' ) {
            $usersCount = $usersCount->onlyTrashed()->count();
        }

        // $usersCount = $usersCount->get();

        return $usersCount;
    }

    /**
     * @param int    $paged
     * @param string $orderBy
     * @param string $sort
     *
     * @return LengthAwarePaginator
     */
    public function getInactivePaginated($paged = 25, $orderBy = 'created_at', $sort = 'desc',$searchbyfirstName='',$searchbylastName='',$searchbyEmail='') : LengthAwarePaginator
    {
        // return $this->model
        //     ->with('roles', 'permissions', 'providers')
        //     ->active(false)
        //     ->orderBy($orderBy, $sort)
        //     ->paginate($paged);

        $users = $this->model->with('roles', 'permissions', 'providers')->active(false);
        if($searchbyEmail!=''){
           $users = $users->where ('email','like','%'.$searchbyEmail.'%');
        }
        if($searchbyfirstName!=''){
            $users = $users->where('first_name','like','%'.$searchbyfirstName.'%');
        }

        if($searchbylastName!=''){
            $users = $users->where('last_name','like','%'.$searchbylastName.'%');
        }

        $users = $users->orderBy($orderBy, $sort);
        $users = $users->paginate($paged);
        return $users;
    }

    /**
     * @param int    $paged
     * @param string $orderBy
     * @param string $sort
     *
     * @return LengthAwarePaginator
     */
    public function getDeletedPaginated($paged = 25, $orderBy = 'created_at', $sort = 'desc') : LengthAwarePaginator
    {
        return $this->model
            ->with('roles', 'permissions', 'providers')
            ->onlyTrashed()
            ->orderBy($orderBy, $sort)
            ->paginate($paged);
    }

    /**
     * @param array $data
     *
     * @return User
     * @throws \Exception
     * @throws \Throwable
     */
    public function create(array $data) : User
    {
        $name = $data['first_name'].' '.$data['last_name'];
        $slug = SlugService::createSlug(User::class, 'slug', $name);

        $data['slug'] = $slug;

        return DB::transaction(function () use ($data) {

            $user = parent::create([
                'first_name' => $data['first_name'],
                'is_sp' => !empty($data['is_sp']) ? $data['is_sp'] : 0,
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'slug' => $data['slug'],
                'password' => Hash::make($data['password']),
                'active' => isset($data['active']) && $data['active'] == '1' ? 1 : 0,
                'confirmation_code' => md5(uniqid(mt_rand(), true)),
                'confirmed' => isset($data['confirmed']) && $data['confirmed'] == '1' ? 1 : 0,
                'avatar_location'=>$data['avatar_location'],
                'avatar_type'=>$data['avatar_type'],
            ]);


            // See if adding any additional permissions
            // if (! isset($data['permissions']) || ! count($data['permissions'])) {
            //     $data['permissions'] = [];
            // }

            if ($user) {
                $profileData = [
                    'user_id' => $user->id,
                ];

                Profile::create($profileData);
                // User must have at least one role
                // if (! count($data['roles'])) {
                //     throw new GeneralException(__('exceptions.backend.access.users.role_needed_create'));
                // }

                // Add selected roles/permissions
                $user->assignRole(2);
                // $user->syncRoles(2);
                // $user->syncPermissions($data['permissions']);

                //Send confirmation email if requested and account approval is off
                // if (isset($data['confirmation_email']) && $user->confirmed == 0 && ! config('access.users.requires_approval')) {
                //     $user->notify(new UserNeedsConfirmation($user->confirmation_code));
                // }

                // event(new UserCreated($user));

                return $user;
            }

            throw new GeneralException('Oops! Something wrong');
        });
    }

    /**
     * @param User  $user
     * @param array $data
     *
     * @return User
     * @throws GeneralException
     * @throws \Exception
     * @throws \Throwable
     */
    public function update( string $id, array $data, $updateFrom, $field ) : User
    {
        $model = $this->model->where($field, $id)->withTrashed()->first();

        if( $model ) {

            $active = isset($data['active']) && !empty($data['active']) ? 1 : 0;
            $confirmed = isset($data['confirmed']) && !empty($data['confirmed']) ? 1 : 0;

            if( request()->routeIs('administrator.backend_update_user_account') ) {

                $updateData = [
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'email' => $data['email'],
                    'active' => $active,
                    'confirmed' => $confirmed,
                ];

                if( !empty( request()->file('avatar_location') ) ){
                    $dir = public_path().'/storage/avatars';
                    // Give permission to avatar folder
                    if(!file_exists($dir)){
                        mkdir($dir, 0755, true);
                    }

                    $path = $model->avatar_location;
                    $parts = explode('/', $path);

                    if( count( $parts ) > 1 ) {
                        if( $parts[1] != 'dummy.png' ) {
                            $fpath = 'storage/'.$model->avatar_location;
                            File::delete($fpath);
                        }
                    }

                    $file = request()->file('avatar_location');
                    $fileusername = strtolower($model->id);
                    $fileusername = str_replace(" ","_",$fileusername);
                    $filename = $fileusername.'_'.time().'.'.$file->getClientOriginalExtension();
                    $file->move($dir ."/", $filename);
                    $updateData['avatar_location'] = 'avatars/'.$filename;
                }

            } elseif ( request()->routeIs('administrator.backend_update_user_security') ) {
                if( !empty( $data['password'] ) ) {
                    $updateData = [
                        'password' => Hash::make($data['password']),
                    ];
                }
            }

            if( !empty( $updateData ) ) {
                $model->where($field, $id)->withTrashed()->update($updateData);
            }

            return $model;
        }

        throw new GeneralException(__('exceptions.backend.access.users.update_error'));
    }

    public function updateProfile( string $id, array $data, $updateFrom, $field ) : Profile
    {
        $model = $this->model->with('profile')->where($field, $id)->withTrashed()->first();

        if( $model ) {

            if( $model->profile ) {
                $profile = Profile::where('user_id', $model->id)->withTrashed()->first();
            } else {
                $profile = new Profile;
                $profile->user_id = $model->id;
            }

            $profile->phone = $data['phone'];
            $profile->experience = $data['experience'];
            $profile->about = $data['about'];
            $profile->address = $data['address'];

            if( $profile->withTrashed()->save() ) {
                return $profile;
            }
        }
    }

    /**
     * @param UUID / ID  $id
     * @param Suspend (delete) / Rise (retrive)  $updateFrom
     * @param Uuid / id  $field
     *
     * @return User
     * @throws GeneralException
     * @throws \Exception
     * @throws \Throwable
     */
    public function userSuspendOrRestore( string $id, $updateFrom, $field )
    {
        $model = $this->model->withTrashed()->where($field, $id)->first();

        if( !empty( $model ) ) {
            switch ($updateFrom) {
                case 'user-suspend':
                    $updateUserSuspendRise = $model->delete();
                    return $updateUserSuspendRise;
                    break;
                case 'user-rise':
                    $updateUserSuspendRise = $model->withTrashed()->restore();
                    return $updateUserSuspendRise;
                    break;
                default:
                    // code...
                    break;
            }

        }
        throw new GeneralException(__('exceptions.backend.access.users.update_error'));
    }

    /**
     * @param User $user
     * @param      $input
     *
     * @return User
     * @throws GeneralException
     */
    public function updatePassword(User $user, $input) : User
    {
        if ($user->update(['password' => $input['password']])) {
            event(new UserPasswordChanged($user));

            return $user;
        }

        throw new GeneralException(__('exceptions.backend.access.users.update_password_error'));
    }

    /**
     * @param User $user
     * @param      $status
     *
     * @return User
     * @throws GeneralException
     */
    public function mark(User $user, $status) : User
    {
        if (auth()->id() == $user->id && $status == 0) {
            throw new GeneralException(__('exceptions.backend.access.users.cant_deactivate_self'));
        }

        $user->active = $status;

        switch ($status) {
            case 0:
                event(new UserDeactivated($user));
            break;

            case 1:
                event(new UserReactivated($user));
            break;
        }

        if ($user->save()) {
            return $user;
        }

        throw new GeneralException(__('exceptions.backend.access.users.mark_error'));
    }

    /**
     * @param User $user
     *
     * @return User
     * @throws GeneralException
     */
    public function confirm(User $user) : User
    {
        if ($user->confirmed) {
            throw new GeneralException(__('exceptions.backend.access.users.already_confirmed'));
        }

        $user->confirmed = 1;
        $confirmed = $user->save();

        if ($confirmed) {
            event(new UserConfirmed($user));

            // Let user know their account was approved
            if (config('access.users.requires_approval')) {
                $user->notify(new UserAccountActive);
            }

            return $user;
        }

        throw new GeneralException(__('exceptions.backend.access.users.cant_confirm'));
    }

    /**
     * @param User $user
     *
     * @return User
     * @throws GeneralException
     */
    public function unconfirm(User $user) : User
    {
        if (! $user->confirmed) {
            throw new GeneralException(__('exceptions.backend.access.users.not_confirmed'));
        }

        if ($user->id == 1) {
            // Cant un-confirm admin
            throw new GeneralException(__('exceptions.backend.access.users.cant_unconfirm_admin'));
        }

        if ($user->id == auth()->id()) {
            // Cant un-confirm self
            throw new GeneralException(__('exceptions.backend.access.users.cant_unconfirm_self'));
        }

        $user->confirmed = 0;
        $unconfirmed = $user->save();

        if ($unconfirmed) {
            event(new UserUnconfirmed($user));

            return $user;
        }

        throw new GeneralException(__('exceptions.backend.access.users.cant_unconfirm'));
    }

    /**
     * @param User $user
     *
     * @return User
     * @throws GeneralException
     * @throws \Exception
     * @throws \Throwable
     */
    public function forceDelete( int $id ) : User
    {
        /*** custom by bindiya ***/
        // if (is_null($user->deleted_at)) {
        //     throw new GeneralException(__('exceptions.backend.access.users.delete_first'));
        // }
        /*** custom by bindiya ***/

        return DB::transaction(function () use ($id) {
            $user = $this->getByColumn('id', $id);
            // dd($user);
        //     // Delete associated relationships
        //     $user->passwordHistories()->delete();
        //     $user->providers()->delete();
        //     $user->sessions()->delete();
            $profile = Profile::where('user_id', $user->id)->first();

            if( $profile ){
                Profile::where('user_id', $user->id)->delete();
            }

            if ($user->forceDelete()) {
                // event(new UserPermanentlyDeleted($user));

                return $user;
            }

            throw new GeneralException(__('exceptions.backend.access.users.delete_error'));
        });
    }

    /**
     * @param User $user
     *
     * @return User
     * @throws GeneralException
     */
    public function restore(User $user) : User
    {
        if (is_null($user->deleted_at)) {
            throw new GeneralException(__('exceptions.backend.access.users.cant_restore'));
        }

        if ($user->restore()) {
            event(new UserRestored($user));

            return $user;
        }

        throw new GeneralException(__('exceptions.backend.access.users.restore_error'));
    }

    /**
     * @param User $user
     * @param      $email
     *
     * @throws GeneralException
     */
    protected function checkUserByEmail(User $user, $email)
    {
        //Figure out if email is not the same
        if ($user->email != $email) {
            //Check to see if email exists
            if ($this->model->where('email', '=', $email)->first()) {
                throw new GeneralException(trans('exceptions.backend.access.users.email_error'));
            }
        }
    }

    /**
     * Import users with it's meta datas
     * @param array $data
     *
     */
    protected function importingUsers(array $data)
    {
        echo "<pre>"; print_r($data); echo "</pre>";
        die("!@#");
        // $model = $this->model;
    }


    /**
     * @param       $id
     * @param array $input
     * @param bool|UploadedFile  $image
     *
     * @return array|bool
     * @throws GeneralException
     */
    public function uploadAvatar($id, array $input, $image = false, $avtar_image =false) {

        $user = $this->getById($id);
        $user->avatar_type = $input['avatar_type'];
        $fileName = '';

        // Upload profile image if necessary
        if ($avtar_image) {
            if (strlen(auth()->user()->avatar_location) > 0) {
                $path = public_path()."/storage/".auth()->user()->avatar_location;
                // Storage::disk('public')->delete(auth()->user()->avatar_location);
                // $deleted = Storage::disk('public')->delete($path);
                $deleted = unlink($path);
                if(!$deleted) {
                    return array('errors' => ['message' => 'Error: while removing image, "'.$path.'"'], 'success' => []);
                }
            }

            $imageData = $avtar_image;
            $fileName =  $id."_".time() .".". explode('/', explode(':', substr($imageData, 0, strpos($imageData, ';')))[1])[1];

            Image::make($avtar_image)->save(public_path('/storage/avatars/').$fileName);

            $user->avatar_location = 'avatars/'.$fileName;
        }

        $user->save();

        return array('success' => ['location' => 'storage/avatars/'.$fileName], 'errors' => []);
    }
}