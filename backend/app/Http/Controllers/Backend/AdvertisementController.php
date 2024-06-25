<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Advertisements;
use App\Repositories\Backend\AdvertisementRepository;
use App\Http\Requests\Backend\Advertisement\StoreAdvertisementRequest;
use App\Http\Requests\Backend\Advertisement\UpdateAdvertisementRequest;

class AdvertisementController extends Controller
{
    /**
     * @var AdvertisementRepository
     */
    protected $advertisementRepository;

    /**
     * AdvertisementController constructor.
     *
     * @param AdvertisementRepository $advertisementRepository
     */
    public function __construct(AdvertisementRepository $advertisementRepository)
    {
        $this->advertisementRepository = $advertisementRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $advertisementsArr = $this->advertisementRepository->getActivePaginated('id', 'desc');
        $getAdvertisements = $advertisementsArr['advertisements'];
        $IsFrontProfile = $advertisementsArr['IsFrontProfile'];
        $IsFrontAds = $advertisementsArr['IsFrontAds'];

        $getSkills = $this->advertisementRepository->getSkillsList();
        return view('backend.advertisements.index', compact('getAdvertisements', 'getSkills', 'IsFrontProfile', 'IsFrontAds'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $getSkills = $this->advertisementRepository->getSkillsList();

        return view('backend.advertisements.create', compact('getSkills'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAdvertisementRequest $request)
    {
        //
        $advertisementStore = $this->advertisementRepository->create($request->all());
        return redirect()->route('administrator.backend_advertisements')->with('success', 'Advertisement created successfully.');
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
    public function edit(string $id)
    {
        //
        $editAdvertisement = $this->advertisementRepository->with('getSkill')->getById($id);
        $getSkills = $this->advertisementRepository->getSkillsList();

        return view('backend.advertisements.edit', compact('editAdvertisement', 'getSkills'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAdvertisementRequest $request, string $id)
    {
        //
        $updateSkill = $this->advertisementRepository->update($id, $request->all(), 'id');
        return redirect()->route('administrator.backend_advertisements')->with('success', 'Advertisement updated successfully.');
    }

    /**
     * Update the specified ads status.
     */
    public function updateAdsStatus(Request $request)
    {
        $updateAdsStatusRes = $this->advertisementRepository->updateAdsStatus($request->all());
        return response()->json($updateAdsStatusRes);
    }

    /**
     * Active Deactive ads on profile page
     */
    public function showProfileFrontAds(Request $request)
    {
        $showProfileFrontAds = $this->advertisementRepository->showProfileFrontAds($request->all());
        return response()->json($showProfileFrontAds);
    }

    /**
     * Active Deactive ads on ads page
     */
    public function showInFrontAds(Request $request)
    {
        $showProfileFrontAds = $this->advertisementRepository->showAdsFrontAds($request->all());
        return response()->json($showProfileFrontAds);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
