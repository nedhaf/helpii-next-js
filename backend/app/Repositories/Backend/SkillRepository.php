<?php

namespace App\Repositories\Backend;

use App\Models\Skill;
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
class SkillRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Skill::class;
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
        $skills = $this->model;

        $skills = $skills->orderBy($orderBy, $sort)->withTrashed()->get();
        // $users = $users->paginate($paged);
        return $skills;
    }

    /**
     * Get all skills for search modal
     *
     * @return mixed
     */
    public function getSearchModalSkills()
    {
        $skills = $this->model;

        $getAllSkills = $skills->allskill();

        return $getAllSkills;
    }

    /**
     * @param array $data
     *
     * @return Skill
     * @throws \Exception
     * @throws \Throwable
     */
    public function create(array $data) : Skill
    {
        $name = $data['name'];
        $active = isset($data['status']) && !empty($data['status']) ? 1 : 0;

        return DB::transaction(function () use ($data, $active) {
            $storeData = [
                'name' => $data['name'],
                'description' => $data['description'],
                'status' => $active,
            ];

            if( !empty( request()->file('avatar') ) ){

                $dir = public_path().'/storage/skills';

                // Give permission to avatar folder
                if(!file_exists($dir)){
                    mkdir($dir, 0755, true);
                }

                $file = request()->file('avatar');
                $fileusername = strtolower($data['name']);
                // $fileusername = str_replace("/","_",$data['name']);
                $fileusername = str_replace("/","_",$fileusername);
                $fileusername = str_replace(" ","_",$fileusername);
                $filename = $fileusername.'_'.time().'.'.$file->getClientOriginalExtension();
                $file->move($dir ."/", $filename);
                $storeData['avatar'] = $filename;
            }
            // $skillCreated = parent::create($storeData);

            if( $skillCreated ) {
                return $skillCreated;
            }

            throw new GeneralException('Oops! Something wrong');
        });
    }

    /**
     * Import Skill from live site and create in existing site
     * @param array $data
     *
     * @return Skill
     * @throws \Exception
     * @throws \Throwable
     */
    public function importSkills(array $data) : Skill
    {
        $name = $data['name'];
        $active = isset($data['status']) && !empty($data['status']) ? 1 : 0;
        return DB::transaction(function () use ($data, $active) {
            $storeData = [
                'id' => $data['id'],
                'name' => $data['name'],
                'description' => $data['description'],
                'avatar' => $data['avatar'],
                'status' => $active,
            ];

            $skillCreated = parent::create($storeData);

            if( $skillCreated ) {
                return $skillCreated;
            }

            throw new GeneralException('Oops! Something wrong');
        });

    }

    /**
     * @param Skill  $skill
     * @param array $data
     *
     * @return User
     * @throws GeneralException
     * @throws \Exception
     * @throws \Throwable
     */
    public function update( string $id, array $data, $field ) : Skill
    {
        $skill = $this->model;
        $skill = $skill->where($field, $id)->first();

        if( $skill ) {
            $active = isset($data['status']) && !empty($data['status']) ? 1 : 0;

            $updateData = [
                'name' => $data['name'],
                'description' => $data['description'],
                'status' => $active,
            ];

            if( !empty( request()->file('avatar') ) ){

                $dir = public_path().'/storage/skills';

                // Give permission to avatar folder
                if(!file_exists($dir)){
                    mkdir($dir, 0755, true);
                }

                $path = $skill->avatar;
                $parts = explode('/', $path);
                if( count( $parts ) > 1 ) {
                    if( $parts[1] != 'dummy.png' || $parts[1] != 'no_img.jpeg' ) {
                        $fpath = 'storage/skills/'.$skill->avatar;
                        File::delete($fpath);
                    }
                } else {
                    if( $skill->avatar != 'dummy.png' || $skill->avatar != 'no_img.jpeg' ) {
                        $fpath = 'storage/skills/'.$skill->avatar;
                        File::delete($fpath);
                    }
                }

                $file = request()->file('avatar');
                $fileusername = strtolower($data['name']);
                // $fileusername = str_replace("/","_",$data['name']);
                $fileusername = str_replace("/","_",$fileusername);
                $fileusername = str_replace(" ","_",$fileusername);
                $filename = $fileusername.'_'.time().'.'.$file->getClientOriginalExtension();
                $file->move($dir ."/", $filename);
                $updateData['avatar'] = $filename;
            }

            if( !empty( $updateData ) ) {
                $skill->where($field, $id)->update($updateData);
            }

            return $skill;
        }
        throw new GeneralException(__('exceptions.backend.access.users.update_error'));
    }

    /**
     * @param Skill $skill
     *
     * @return Skill
     * @throws GeneralException
     * @throws \Exception
     * @throws \Throwable
     */
    public function forceDelete( int $id ) : Skill
    {
        return DB::transaction(function () use ($id) {
            $skill = $this->getByColumn('id', $id);

            if ($skill->forceDelete()) {
                return $skill;
            }

            throw new GeneralException(__('exceptions.backend.access.users.delete_error'));
        });
    }
}