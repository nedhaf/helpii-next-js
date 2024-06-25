<?php

namespace App\Http\Controllers\Backend\Skill;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Skill;
use App\Repositories\Backend\SkillRepository;
use App\Http\Requests\Backend\Skill\StoreSkillRequest;
use App\Http\Requests\Backend\Skill\UpdateSkillRequest;

class SkillController extends Controller
{
    /**
     * @var SkillRepository
     */
    protected $skillRepository;

    /**
     * SkillController constructor.
     *
     * @param SkillRepository $skillRepository
     */
    public function __construct(SkillRepository $skillRepository)
    {
        $this->skillRepository = $skillRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $getSkills = $this->skillRepository->getActivePaginated('id', 'desc');

        return view('backend.skills.index', compact("getSkills"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('backend.skills.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSkillRequest $request)
    {
        $skillStore = $this->skillRepository->create($request->all());
        //
        return redirect()->route('administrator.backend_skills')->with('success', 'Skill created successfully.');
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
        $editSkill = $this->skillRepository->getByColumn('id', $id);

        return view('backend.skills.edit',  compact('editSkill'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSkillRequest $request, string $id)
    {
        //
        $updateSkill = $this->skillRepository->update($id, $request->all(), 'id');

        return redirect()->route('administrator.backend_skills')->with('success', 'Skill updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $deleteSkill = $this->skillRepository->forceDelete($id);
        $status = "success";
        $msg = "Skill deleted successfully.";

        return redirect()->route('administrator.backend_skills')->with($status, $msg);
    }

    /**
     * Import Skill from live site
     */
    public function importSkill()
    {
        $endpoint = "https://www.helpii.se/api/get-skills-for-import";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec ($ch);
        $err = curl_error($ch);  //if you need
        curl_close ($ch);
        $Skills = json_decode($response, true);
        // dd($Skills->skills);
        // $status = false;
        foreach ($Skills as $key => $skills) {
            if( $key == 'skills' ) {
                foreach ($skills as $skey => $skill) {
                    // echo "<pre>"; print_r($skill); echo "</pre>";
                    $skillStore = $this->skillRepository->importSkills($skill);
                }
            }
        }
        return response()->json([
            'status' => 1,
            "message" => "Success"
        ]);
    }
}
