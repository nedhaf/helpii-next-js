<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\AdministratorController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\Auth\UserManagement\UserManagementController;
use App\Http\Controllers\Backend\Skill\SkillController;
use App\Http\Controllers\Backend\Badge\UserBadgeController;
use App\Http\Controllers\Backend\SiteSettingController;
use App\Http\Controllers\Backend\AdvertisementController;

/*
|--------------------------------------------------------------------------
| Administrator Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/*
 * Backend Routes
 * Namespaces indicate folder structure
 */

Route::group(['prefix' => 'administrator', 'as' => 'administrator.' ], function () {
    // Auth::routes();
    /*Route::get('/', function(){
        return view('backend.authentications.login');
    });*/
    Route::get('/', [AdministratorController::class, 'login'])->name('backend_login_form');
    Route::post('/', [AdministratorController::class, 'authentication'])->name('backend_login');
});

Route::group(['namespace' => 'Backend', 'prefix' => 'administrator', 'as' => 'administrator.', 'middleware' => ['auth', 'role:administrator'] ], function () {
    // Auth::routes();
    Route::post('logout', [AdministratorController::class, 'logout'])->name('backend_logout');
    // Switch between the included languages
    Route::get('lang/{lang}', [LanguageController::class, 'swap'])->name('swap-lang');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('backend_dashboard');

    // User Management Routes
    Route::group([ 'prefix' => 'user-management' ], function(){
        Route::get('/', [UserManagementController::class, 'index'])->name('backend_user_management');
        Route::get('create-user', [UserManagementController::class, 'create'])->name('backend_create_user');
        Route::post('store-user', [UserManagementController::class, 'store'])->name('backend_store_user');
        Route::delete('delete-user/{delete_id}', [UserManagementController::class, 'destroy'])->name('backend_destroy_user');
        Route::group([ 'prefix' => 'edit-user' ], function(){
            // Basic details
            Route::get('account/{id}', [UserManagementController::class, 'edit'])->name('backend_edit_user');
            Route::post('update-user-account/{id}', [UserManagementController::class, 'update'])->name('backend_update_user_account');

            // Update profile details
             Route::post('update-user-profile/{id}', [UserManagementController::class, 'updateProfile'])->name('backend_update_user_profile');

            // Password route
            Route::get('security/{id}', [UserManagementController::class, 'edit'])->name('backend_edit_user_security');
            Route::post('update-security/{id}', [UserManagementController::class, 'update'])->name('backend_update_user_security');

            // Connections route
            Route::get('connections/{id}', [UserManagementController::class, 'edit'])->name('backend_edit_user_connection');
            Route::post('update-connections/{id}', [UserManagementController::class, 'update'])->name('backend_update_user_connection');

            // Suspend User
            Route::post('suspend/{id}', [UserManagementController::class, 'userSuspendRise'])->name('backend_suspend_user');
            Route::post('rise/{id}', [UserManagementController::class, 'userSuspendRise'])->name('backend_rise_user');
        });
        Route::post('import-users', [UserManagementController::class, 'importUsers'])->name('backend_import_users');
    });

    // Advertisements
    Route::group([ 'prefix' => 'advertisement-management' ], function(){
        Route::get('advertisements', [AdvertisementController::class, 'index'])->name('backend_advertisements');
        Route::get('create-advertisement', [AdvertisementController::class, 'create'])->name('backend_advertisement_create');
        Route::post('store-advertisement', [AdvertisementController::class, 'store'])->name('backend_store_advertisement');
        Route::get('edit-advertisement/{id}', [AdvertisementController::class, 'edit'])->name('backend_advertisement_edit');
        Route::post('update-advertisement/{id}', [AdvertisementController::class, 'update'])->name('backend_advertisement_update');

        Route::post('update-ad-status', [AdvertisementController::class, 'updateAdsStatus'])->name('backend_ads_update_status');
        Route::post('is-front-profile-advertisement', [AdvertisementController::class, 'showProfileFrontAds'])->name('admin-front-profile-advertisement');
        Route::post('is-front-ads-advertisement', [AdvertisementController::class, 'showInFrontAds'])->name('admin-front-ads-advertisement');
    });

    // Skills
    Route::group([ 'prefix' => 'skill-management' ], function(){
        Route::get('skills', [SkillController::class, 'index'])->name('backend_skills');
        Route::get('create-skill', [SkillController::class, 'create'])->name('backend_skill_create');
        Route::post('store-skill', [SkillController::class, 'store'])->name('backend_store_skill');
        Route::get('edit-skill/{id}', [SkillController::class, 'edit'])->name('backend_skill_edit');
        Route::post('update-skill/{id}', [SkillController::class, 'update'])->name('backend_update_skill');
        Route::delete('delete-skill/{id}', [SkillController::class, 'destroy'])->name('backend_destroy_skill');

        Route::post('import-skill', [SkillController::class, 'importSkill'])->name('backend_import_skill');
    });

    // Badges
    Route::group([ 'prefix' => 'badge-management' ], function(){
        Route::get('badges', [UserBadgeController::class, 'index'])->name('backend_badges');
        Route::get('create-badge', [UserBadgeController::class, 'create'])->name('backend_badge_create');
        Route::post('store-badge', [UserBadgeController::class, 'store'])->name('backend_store_badge');
        Route::get('edit-badge/{id}', [UserBadgeController::class, 'edit'])->name('backend_badge_edit');
        Route::post('update-badge/{id}', [UserBadgeController::class, 'update'])->name('backend_update_badge');
        Route::delete('delete-badge/{id}', [UserBadgeController::class, 'destroy'])->name('backend_destroy_badge');

        Route::post('import-badges', [UserBadgeController::class, 'importBadges'])->name('backend_import_badges');
    });

    // SIte settings
    Route::group([ 'prefix' => 'site-settings' ], function(){
        Route::get('basic-settings', [SiteSettingController::class, 'BasicSettingsIndex'])->name('backend_basic_settings');
        Route::post('store-basic-settings', [SiteSettingController::class, 'store'])->name('backend_store_basic_settings');
    });
});