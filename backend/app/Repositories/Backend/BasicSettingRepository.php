<?php

namespace App\Repositories\Backend;

use App\Models\Sitesettings;
use Illuminate\Support\Facades\DB;
use App\Exceptions\GeneralException;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\File;

/**
 * Class AdvertisementRepository.
 */

class BasicSettingRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Sitesettings::class;
    }

    /**
     * @param string $orderBy
     * @param string $sort
     *
     * @return mixed
     */

    public function getBasicSettings()
    {
        $basicsettings = $this->model;

        $getBasicSettings = $basicsettings->get();
        $getBasicSettings = reset($getBasicSettings);

        // Create empty array for basic settings
        $settingArr = array();

        // Create empty basic settings value
        $settingArr['title'] = '';
        $settingArr['email'] = '';
        $settingArr['phone'] = '';
        $settingArr['facebookurl'] = '';
        $settingArr['twitterurl'] = '';
        $settingArr['linkdinurl'] = '';
        $settingArr['instagramurl'] = '';
        $settingArr['description'] = '';
        $settingArr['address'] = '';
        $settingArr['tag'] = '';
        $settingArr['sitelogo'] = '';
        $settingArr['backgroudimage'] = '';
        $settingArr['backgroudcolor'] = '';

        // Fill basic settings value if not empty
        if( !empty( $getBasicSettings ) ) {
            foreach ($getBasicSettings as $basicsetting) {
                // title
                if( $basicsetting->fieldname == "title" ) {
                    $settingArr['title'] = $basicsetting->fieldvalue;
                }

                // email
                if( $basicsetting->fieldname == "email" ) {
                    $settingArr['email'] = $basicsetting->fieldvalue;
                }

                // phone
                if( $basicsetting->fieldname == "phone" ) {
                    $settingArr['phone'] = $basicsetting->fieldvalue;
                }

                // facebookurl
                if( $basicsetting->fieldname == "facebookurl" ) {
                    $settingArr['facebookurl'] = $basicsetting->fieldvalue;
                }

                // twitterurl
                if( $basicsetting->fieldname == "twitterurl" ) {
                    $settingArr['twitterurl'] = $basicsetting->fieldvalue;
                }

                // linkdinurl
                if( $basicsetting->fieldname == "linkdinurl" ) {
                    $settingArr['linkdinurl'] = $basicsetting->fieldvalue;
                }

                // instagramurl
                if( $basicsetting->fieldname == "instagramurl" ) {
                    $settingArr['instagramurl'] = $basicsetting->fieldvalue;
                }

                // description
                if( $basicsetting->fieldname == "description" ) {
                    $settingArr['description'] = $basicsetting->fieldvalue;
                }

                // address
                if( $basicsetting->fieldname == "address" ) {
                    $settingArr['address'] = $basicsetting->fieldvalue;
                }

                // tag
                if( $basicsetting->fieldname == "tag" ) {
                    $settingArr['tag'] = $basicsetting->fieldvalue;
                }

                // sitelogo
                if( $basicsetting->fieldname == "sitelogo" ) {
                    $settingArr['sitelogo'] = $basicsetting->fieldvalue;
                }

                // backgroudimage
                if( $basicsetting->fieldname == "backgroudimage" ) {
                    $settingArr['backgroudimage'] = $basicsetting->fieldvalue;
                }

                // backgroudcolor
                if( $basicsetting->fieldname == "backgroudcolor" ) {
                    $settingArr['backgroudcolor'] = $basicsetting->fieldvalue;
                }
            }
        }

        return $settingArr;
    }


    /**
     * @param array $data
     *
     * @return Sitesettings
     * @throws \Exception
     * @throws \Throwable
     */
    public function createUpdate(array $data) : Sitesettings
    {
        $basicsettings = $this->model;

        $getBasicSettings = $basicsettings->get();
        $getBasicSettings = reset($getBasicSettings);

        if(!empty($getBasicSettings))
        {
            $basicsettings->query()->truncate();
        }

        $previous_logo = $data['previous_logo'];
        $previous_backgroudimage = $data['previous_backgroudimage'];

        foreach( $data as $key => $setting ) {
            // Text fileds
            if( $key != '_token' && $key != 'sitelogo' && $key != 'backgroudimage' ) {
                $Sitesettings = new $basicsettings;

                $tag = '';

                if( $key == 'tag' ) {
                    $Tags = json_decode($setting, true);
                    $TagsArr = array();
                    foreach( $Tags as $tags ) {
                        $TagsArr[] = $tags['value'];
                    }
                    $tag = implode(',', $TagsArr);

                    $Sitesettings->fieldname = $key;
                    $Sitesettings->fieldvalue = $tag;
                } else {
                    $Sitesettings->fieldname = $key;
                    $Sitesettings->fieldvalue = $setting;
                }
                $Sitesettings->save();
            }

            // sitelogo Field
            if( !empty( request()->file('sitelogo') ) && $key == 'sitelogo' ) {
                $Sitesettings = new $basicsettings;

                $dir = public_path().'/storage/site-settings';

                // Give permission to avatar folder
                if(!file_exists($dir)){
                    mkdir($dir, 0755, true);
                }

                $file = request()->file('sitelogo');
                $filename = 'site_logo_'.time().'.'.$file->getClientOriginalExtension();
                $file->move($dir ."/", $filename);
                $Sitesettings->fieldname = $key;
                $Sitesettings->fieldvalue = $filename;
                $Sitesettings->save();
            } else {
                $Sitesettings = new $basicsettings;
                $Sitesettings->fieldname = 'sitelogo';
                $Sitesettings->fieldvalue = $previous_logo;
                $Sitesettings->save();
            }

            // backgroudimage Field
            if(  !empty( request()->file('backgroudimage') ) && $key == 'backgroudimage' ) {
                $Sitesettings = new $basicsettings;

                $dir = public_path().'/storage/site-settings';

                // Give permission to avatar folder
                if(!file_exists($dir)){
                    mkdir($dir, 0755, true);
                }

                $file = request()->file('backgroudimage');
                $filename = 'background_img_'.time().'.'.$file->getClientOriginalExtension();
                $file->move($dir ."/", $filename);
                $Sitesettings->fieldname = $key;
                $Sitesettings->fieldvalue = $filename;
                $Sitesettings->save();
            } else {
                $Sitesettings = new $basicsettings;
                $Sitesettings->fieldname = 'backgroudimage';
                $Sitesettings->fieldvalue = $previous_backgroudimage;
                $Sitesettings->save();
            }
        }

        return $Sitesettings;
    }

    /**
     * @param Sitesettings  $advertisement
     * @param array $data
     *
     * @return Sitesettings
     * @throws GeneralException
     * @throws \Exception
     * @throws \Throwable
     */
    public function update( string $id, array $data, $field ) : Sitesettings
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
}