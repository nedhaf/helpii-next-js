<?php

namespace App\Repositories\Backend;

use App\Models\Advertisements;
use App\Models\Skill;
use Illuminate\Support\Facades\DB;
use App\Exceptions\GeneralException;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\File;

/**
 * Class AdvertisementRepository.
 */

class AdvertisementRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Advertisements::class;
    }

    /**
     * @param string $orderBy
     * @param string $sort
     *
     * @return mixed
     */

    public function getActivePaginated( $orderBy = 'created_at', $sort = 'desc', $searchbyStatus ='' )
    {
        $advertisements = $this->model;

        // Get All adds
        $arr['advertisements'] = $advertisements->with('getSkill')->orderBy($orderBy, $sort)->get();
        // Get first ad which is for front profile
        $arr['IsFrontProfile'] = $advertisements->where('show_in_front_profile', 1)->first();
        // Get first ad which is for front ads
        $arr['IsFrontAds'] = $advertisements->where('show_in_front_ads', 1)->first();

        return $arr;
    }

    /**
     * @param string $orderBy
     * @param string $sort
     *
     * @return mixed
     */
    public function getSkillsList( $orderBy = 'created_at', $sort = 'desc', $status ='' )
    {

        if( !empty( $status ) ) {
            $skills = Skill::where('status', $status)->orderBy($orderBy, $sort)->withTrashed()->get();
        } else {
            $skills = Skill::withTrashed()->orderBy($orderBy, $sort)->get();
        }

        return $skills;
    }

    /**
     * @param array $data
     *
     * @return Advertisements
     * @throws \Exception
     * @throws \Throwable
     */
    public function create(array $data) : Advertisements
    {
        return DB::transaction(function () use ($data) {
            $storeData = [
                'title' => $data['title'],
                'user_id' => auth()->id(),
                'skill_id' => $data['skill_id'],
                'phone' => $data['phone'],
                'link' => $data['link'],
                'city' => $data['city'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'position' => $data['position'],
                'cost' => $data['cost'],
                'color' => $data['color'],
                'description' => $data['description'],
            ];

            if( !empty( request()->file('image') ) ){
                $dir = public_path().'/storage/advertisement/image';

                // Give permission to avatar folder
                if(!file_exists($dir)){
                    mkdir($dir, 0755, true);
                }

                $file = request()->file('image');
                $filename = time().'.'.$file->getClientOriginalExtension();
                $file->move($dir ."/", $filename);
                $storeData['image'] = $filename;
            }

            if( !empty( request()->file('badge_img') ) ){
                $dir = public_path().'/storage/advertisement/badgeImg';

                // Give permission to avatar folder
                if(!file_exists($dir)){
                    mkdir($dir, 0755, true);
                }

                $file = request()->file('badge_img');
                $filename = time().'.'.$file->getClientOriginalExtension();
                $file->move($dir ."/", $filename);
                $storeData['badge_img'] = $filename;
            }

            $advertisementCreated = parent::create($storeData);

            if( $advertisementCreated ) {
                return $advertisementCreated;
            }

            throw new GeneralException('Oops! Something wrong');
        });
    }

    /**
     * @param Advertisements  $advertisement
     * @param array $data
     *
     * @return Advertisements
     * @throws GeneralException
     * @throws \Exception
     * @throws \Throwable
     */
    public function update( string $id, array $data, $field ) : Advertisements
    {
        $advertisements = $this->model;
        $ads = $advertisements->where('id', $id)->first();

        if( $ads ) {
            $ads->title = $data['title'];
            $ads->skill_id = $data['skill_id'];
            $ads->phone = $data['phone'];
            $ads->city = $data['city'];
            $ads->link = $data['link'];
            $ads->start_date = $data['start_date'];
            $ads->end_date = $data['end_date'];
            $ads->cost = $data['cost'];
            $ads->position = $data['position'];
            $ads->color = $data['color'];
            $ads->description = $data['description'];

            if( !empty( request()->file('image') ) ){
                $dir = public_path().'/storage/advertisement/image';

                // Give permission to avatar folder
                if(!file_exists($dir)){
                    mkdir($dir, 0755, true);
                }

                $path = $ads->image;
                $parts = explode('/', $path);
                $fpath = $dir.'/'.$path;
                File::delete($fpath);

                $file = request()->file('image');
                $filename = time().'.'.$file->getClientOriginalExtension();
                $file->move($dir ."/", $filename);
                $ads->image = $filename;
            }

            if( !empty( request()->file('badge_img') ) ){
                $dir = public_path().'/storage/advertisement/badgeImg';

                // Give permission to avatar folder
                if(!file_exists($dir)){
                    mkdir($dir, 0755, true);
                }

                $path = $ads->badge_img;
                $parts = explode('/', $path);
                $fpath = $dir.'/'.$path;
                File::delete($fpath);

                $file = request()->file('badge_img');
                $filename = time().'.'.$file->getClientOriginalExtension();
                $file->move($dir ."/", $filename);
                $ads->badge_img = $filename;
            }

            if( $ads->save() ){
                return $ads;
            }
        }
    }

    /**
     * @param Advertisements  $advertisement
     * @param array $data
     *
     * @return Advertisements
     * @throws GeneralException
     * @throws \Exception
     * @throws \Throwable
     */
    public function updateAdsStatus( array $data ) : array
    {
        $advertisements = $this->model;
        $getAds = $advertisements->where('id', $data['ads_id'])->first();

        if( $getAds->status === 1 ) {
            $getAds->status = 0;
        } else {
            $getAds->status = 1;
        }

        $resultArr = array();
        try{

            if( $getAds->save() ) {
                $resultArr = [
                    'errors' => '',
                    "status" => 200,
                    "results" => 'success',
                    "message" => 'Ads status updated successfully.',
                ];
            } else {
                $resultArr = [
                    'errors' => 'Opps! Something went wrong!',
                    "status" => 200,
                    "results" => 'error',
                    "message" => '',
                ];
            }
        } catch(Exceptions $e ){
            $resultArr = [
                'errors' => $e->getMessage(),
                "status" => 200,
                "results" => 'error',
                "message" => '',
            ];
        }

        return $resultArr;
    }

    /**
     * Function for display ads on Profile page
     *
     * @param Advertisements  $advertisement
     * @param array $data
     *
     * @return Advertisements
     * @throws GeneralException
     * @throws \Exception
     * @throws \Throwable
     */
    public function showProfileFrontAds( $data ) : array
    {
        $advertisements = $this->model;
        $getIsFrontAdv = $advertisements->where('id', $data['ads_id'])->first();

        if( $getIsFrontAdv->show_in_front_profile === 1 ) {
            $getIsFrontAdv->show_in_front_profile = 0;
        } else {
            $getIsFrontAdv->show_in_front_profile = 1;
        }

        $getActiveFrontProfileAds = $advertisements->where('show_in_front_profile', 1)->get();

        foreach ($getActiveFrontProfileAds as $key => $value) {
            if( $value != $data['ads_id'] ) {
                $updateData = [
                    'show_in_front_profile' => 0,
                ];
                $advertisements->where('id', $value->id)->update($updateData);
            }
        }

        $resultArr = array();
        if( $getIsFrontAdv->save() ) {
            $resultArr = [
                'errors' => '',
                "status" => 200,
                "results" => 'success',
                "message" => 'Other activated advertisement for profile is deactivated for fornt side.',
            ];
        } else {
            $resultArr = [
                'errors' => 'Opps! Something went wrong!',
                "status" => 200,
                "results" => 'error',
                "message" => '',
            ];
        }

        return $resultArr;
    }

    /**
     * Function for display ads on Ads page
     *
     * @param Advertisements  $advertisement
     * @param array $data
     *
     * @return Advertisements
     * @throws GeneralException
     * @throws \Exception
     * @throws \Throwable
     */
    public function showAdsFrontAds( $data ) : array
    {
        $advertisements = $this->model;
        $getIsFrontAdv = $advertisements->where('id', $data['ads_id'])->first();

        if( $getIsFrontAdv->show_in_front_ads === 1 ) {
            $getIsFrontAdv->show_in_front_ads = 0;
        } else {
            $getIsFrontAdv->show_in_front_ads = 1;
        }

        $getActiveFrontProfileAds = $advertisements->where('show_in_front_ads', 1)->get();

        foreach ($getActiveFrontProfileAds as $key => $value) {
            if( $value != $data['ads_id'] ) {
                $updateData = [
                    'show_in_front_ads' => 0,
                ];
                $advertisements->where('id', $value->id)->update($updateData);
            }
        }

        $resultArr = array();
        if( $getIsFrontAdv->save() ) {
            $resultArr = [
                'errors' => '',
                "status" => 200,
                "results" => 'success',
                "message" => 'Other activated advertisement for ads is deactivated for fornt side.',
            ];
        } else {
            $resultArr = [
                'errors' => 'Opps! Something went wrong!',
                "status" => 200,
                "results" => 'error',
                "message" => '',
            ];
        }

        return $resultArr;
    }
}