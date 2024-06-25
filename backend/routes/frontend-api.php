<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\GeneralController;

/*
|--------------------------------------------------------------------------
| Frontend Api Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Get all skills
Route::get('getSkills', [GeneralController::class, 'getSkills'])->name('frontAllSkills');
Route::get('getBadges', [GeneralController::class, 'getBadges'])->name('frontAllBadge');