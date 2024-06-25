<?php

namespace App\Repositories\Backend;

use App\Models\UserBadge;
use App\Models\Auth\User;
use App\Models\Profile;
use Illuminate\Support\Facades\DB;
use App\Exceptions\GeneralException;
use Illuminate\Support\Facades\Hash;
use App\Repositories\BaseRepository;
use \Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Support\Facades\File;

/**
 * Class SkillRepository.
 */
class BadgeRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return UserBadge::class;
    }

    /**
     * @param string $orderBy
     * @param string $sort
     *
     * @return mixed
     */

    public function getActivePaginated( $orderBy = 'created_at', $sort = 'desc', $searchbyStatus ='' )
    {

        // $users = $this->model->with('roles')->whereHas("roles", function($q){ $q->whereNot("id", 1); })->active();
        $badge = $this->model;

        $badges = $badge->orderBy($orderBy, $sort)->get();
        // $users = $users->paginate($paged);
        return $badges;
    }

    /**
     * Get all badges for search modal
     *
     * @return mixed
     */
    public function getSearchModalBadges()
    {
        $badge = $this->model;

        $getAllBadges = $badge->allbadge();

        return $getAllBadges;
    }

    /**
     * @param array $data
     *
     * @return UserBadge
     * @throws \Exception
     * @throws \Throwable
     */
    public function create(array $data) : UserBadge
    {
        $name = $data['badge_name'];
        $active = isset($data['status']) && !empty($data['status']) ? 1 : 0;

        return DB::transaction(function () use ($data, $active) {
            $storeData = [
                'badge_name' => $data['badge_name'],
                'status' => $active,
            ];

            if( !empty( request()->file('image') ) ){

                $dir = public_path().'/storage/badges';

                // Give permission to avatar folder
                if(!file_exists($dir)){
                    mkdir($dir, 0755, true);
                }

                $file = request()->file('image');
                $fileusername = strtolower($data['badge_name']);
                $fileusername = str_replace("/","_",$fileusername);
                $fileusername = str_replace(" ","_",$fileusername);
                $filename = $fileusername.'_'.time().'.'.$file->getClientOriginalExtension();
                $file->move($dir ."/", $filename);
                $storeData['image'] = $filename;
            }

            $badgeCreated = parent::create($storeData);

            if( $badgeCreated ) {
                return $badgeCreated;
            }

            throw new GeneralException('Oops! Something wrong');
        });
    }

    /**
     * Import Badges from live site and create in existing site
     * @param array $data
     *
     * @return UserBadge
     * @throws \Exception
     * @throws \Throwable
     */
    public function importBadges(array $data) : UserBadge
    {
        $active = isset($data['status']) && !empty($data['status']) ? 1 : 0;
        return DB::transaction(function () use ($data, $active) {
            $storeData = [
                'id' => $data['id'],
                'badge_name' => $data['badge_name'],
                'image' => 'no_img.jpeg',
                'status' => $active,
            ];

            $badgeCreated = parent::create($storeData);

            if( $badgeCreated ) {
                return $badgeCreated;
            }

            throw new GeneralException('Oops! Something wrong');
        });

    }

    /**
     * @param UserBadge  $badge
     * @param array $data
     *
     * @return UserBadge
     * @throws GeneralException
     * @throws \Exception
     * @throws \Throwable
     */
    public function update( string $id, array $data, $field ) : UserBadge
    {
        $badge = $this->model;
        $badge = $badge->where($field, $id)->first();

        if( $badge ) {
            $active = isset($data['status']) && !empty($data['status']) ? 1 : 0;

            $updateData = [
                'badge_name' => $data['badge_name'],
                'status' => $active,
            ];

            if( !empty( request()->file('image') ) ){

                $dir = public_path().'/storage/badges';

                // Give permission to avatar folder
                if(!file_exists($dir)){
                    mkdir($dir, 0755, true);
                }

                $path = $badge->image;
                $parts = explode('/', $path);
                if( count( $parts ) > 1 ) {
                    if( $parts[1] != 'dummy.png' || $parts[1] != 'no_img.jpeg' ) {
                        $fpath = 'storage/badges/'.$badge->image;
                        File::delete($fpath);
                    }
                } else {
                    if( $badge->image != 'dummy.png' || $badge->image != 'no_img.jpeg' ) {
                        $fpath = 'storage/badges/'.$badge->image;
                        File::delete($fpath);
                    }
                }

                $file = request()->file('image');
                $fileusername = strtolower($data['badge_name']);
                $fileusername = str_replace("/","_",$fileusername);
                $fileusername = str_replace(" ","_",$fileusername);
                $filename = $fileusername.'_'.time().'.'.$file->getClientOriginalExtension();
                $file->move($dir ."/", $filename);
                $updateData['image'] = $filename;
            }

            if( !empty( $updateData ) ) {
                $badge->where($field, $id)->update($updateData);
            }

            return $badge;
        }
        throw new GeneralException(__('exceptions.backend.access.users.update_error'));
    }

    /**
     * @param UserBadge $badge
     *
     * @return UserBadge
     * @throws GeneralException
     * @throws \Exception
     * @throws \Throwable
     */
    public function forceDelete( int $id ) : UserBadge
    {
        return DB::transaction(function () use ($id) {
            $badge = $this->getById($id);

            if ($badge->forceDelete()) {
                return $badge;
            }

            throw new GeneralException(__('exceptions.backend.access.users.delete_error'));
        });
    }
}