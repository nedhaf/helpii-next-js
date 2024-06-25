<?php

namespace App\Http\Controllers\Backend\Badge;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Skill;
use App\Repositories\Backend\BadgeRepository;
use App\Http\Requests\Backend\Badge\StoreBadgeRequest;
use App\Http\Requests\Backend\Badge\UpdateBadgeRequest;

class UserBadgeController extends Controller
{
    /**
     * @var BadgeRepository
     */
    protected $badgeRepository;

    /**
     * BadgeController constructor.
     *
     * @param BadgeRepository $badgeRepository
     */
    public function __construct(BadgeRepository $badgeRepository)
    {
        $this->badgeRepository = $badgeRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $getBadges = $this->badgeRepository->getActivePaginated('id', 'desc');
        return view('backend.badges.index', compact("getBadges"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('backend.badges.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBadgeRequest $request)
    {
        //
        $badgeStore = $this->badgeRepository->create($request->all());
        return redirect()->route('administrator.backend_badges')->with('success', 'Badge created successfully.');
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
        $editBadge = $this->badgeRepository->getById($id);
        return view('backend.badges.edit',  compact('editBadge'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBadgeRequest $request, string $id)
    {
        $updateBadge = $this->badgeRepository->update($id, $request->all(), 'id');
        return redirect()->route('administrator.backend_badges')->with('success', 'Badge updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $deleteBadge = $this->badgeRepository->forceDelete($id);
        $status = "success";
        $msg = "Badge deleted successfully.";

        return redirect()->route('administrator.backend_badges')->with($status, $msg);
    }

    /**
     * Import Badges from live site
     */
    public function importBadges()
    {
        $endpoint = "https://www.helpii.se/api/get-badges-for-import";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec ($ch);
        $err = curl_error($ch);  //if you need
        curl_close ($ch);
        $Badges = json_decode($response, true);
        // dd($Skills->skills);
        // $status = false;
        foreach ($Badges as $key => $badges) {
            if( $key == 'badges' ) {
                foreach ($badges as $bkey => $badge) {
                    // echo "<pre>"; print_r($badge); echo "</pre>";
                    $badgesStore = $this->badgeRepository->importBadges($badge);
                }
            }
        }
        // die('Done!');
        return response()->json([
            'status' => "OK",
            "message" => "Success"
        ], 200);
    }
}
