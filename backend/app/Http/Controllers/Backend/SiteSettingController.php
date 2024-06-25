<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sitesettings;
use App\Repositories\Backend\BasicSettingRepository;
// use App\Http\Requests\Backend\Advertisement\StoreAdvertisementRequest;
// use App\Http\Requests\Backend\Advertisement\UpdateAdvertisementRequest;

class SiteSettingController extends Controller
{
    /**
     * @var BasicSettingRepository
     */
    protected $basicsettingRepository;

    /**
     * SiteSettingController constructor.
     *
     * @param BasicSettingRepository $basicsettingRepository
     */
    public function __construct(BasicSettingRepository $basicsettingRepository)
    {
        $this->basicsettingRepository = $basicsettingRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function BasicSettingsIndex()
    {
        //
        $basicsettings = $this->basicsettingRepository->getBasicSettings();

        return view('backend.site-settings.basic-settings.index', compact('basicsettings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $basicsettingStore = $this->basicsettingRepository->createUpdate($request->all());

        return redirect()->route('administrator.backend_basic_settings')->with('success', 'Basic settings saved successfully.');
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
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
