<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Frontend\BasicSettingRepository;
use App\Repositories\Frontend\GuestUserRepository;
use Inertia\Inertia as Inertia;

class HomeController extends Controller
{
    /**
     * @var BasicSettingRepository
     */
    protected $basicsettingRepository, $guestuserRepository;

    /**
     * HomeController constructor.
     *
     * @param BasicSettingRepository $basicsettingRepository
     */
    public function __construct(BasicSettingRepository $basicsettingRepository, GuestUserRepository $guestuserRepository)
    {
        $this->basicsettingRepository = $basicsettingRepository;
        $this->guestuserRepository = $guestuserRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //Get ip address
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip_address = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip_address = $_SERVER['REMOTE_ADDR'];
        }
        // Generate url
        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        // Insert Ip address for guest user
        $data   =   array(
            'ip_address'    =>  $ip_address,
            "url"           =>  $actual_link
        );
        $createguestuser = $this->guestuserRepository->create($data);

        // Get site basic settings
        $basicsettings = $this->basicsettingRepository->getBasicSettings();

        // return view('frontend.home');
        return Inertia::render('Index', [
            'user' => \Auth::user(),
            'currentRoute' => request()->getRequestUri(),
            'siteSettings' => $basicsettings,
        ]);
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
