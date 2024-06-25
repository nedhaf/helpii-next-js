<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Backend\SkillRepository;
use App\Repositories\Backend\BadgeRepository;

class GeneralController extends Controller
{
    //

    /**
     * @var SkillRepository
     */
    protected $skillRepository, $badgeRepository;

    /**
     * GeneralController constructor.
     *
     * @param SkillRepository $skillRepository
     */
    public function __construct(SkillRepository $skillRepository, BadgeRepository $badgeRepository)
    {
        $this->skillRepository = $skillRepository;
        $this->badgeRepository = $badgeRepository;
    }

    /**
     * Get all skills.
     */
    public function getSkills(Request $request)
    {
        $getSkills = $this->skillRepository->getSearchModalSkills();

        return response()->json([
            'status' => 1,
            'msg' => 'Skill found.',
            'results' => $getSkills
        ]);
    }

    public function getBadges(Request $request)
    {
        $getBadges = $this->badgeRepository->getSearchModalBadges();

        return response()->json([
            'status' => 1,
            'msg' => 'Badges found.',
            'results' => $getBadges
        ]);
    }
}
