<?php

namespace App\Repositories\Frontend;

use App\Models\GuestUser;
use Illuminate\Support\Facades\DB;
use App\Exceptions\GeneralException;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\File;

/**
 * Class AdvertisementRepository.
 */

class GuestUserRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return GuestUser::class;
    }

    /**
     * @param string $orderBy
     * @param string $sort
     *
     * @return mixed
     */

    public function getGuestUsers()
    {
        $guestusers = $this->model;
        return $guestusers;
    }


    /**
     * @param array $data
     *
     * @return GuestUser
     * @throws \Exception
     * @throws \Throwable
     */
    public function create(array $data) : GuestUser
    {
        $guestusers = $this->model;

        return DB::transaction(function () use ($data) {
            $storeData = [
                'ip_address' => $data['ip_address'],
                'url' => $data['url'],
            ];

            $guestuserCreated = parent::create($storeData);

            if( $guestuserCreated ) {
                return $guestuserCreated;
            }

            throw new GeneralException('Oops! Something wrong');
        });
    }
}