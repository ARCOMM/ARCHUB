<?php

use App\Http\Controllers\Missions\MediaController;
use App\Http\Controllers\Missions\MissionController;
use App\Http\Controllers\Missions\MissionTagController;
use App\Http\Controllers\Missions\OperationController;
use App\Http\Controllers\Users\UserController;
use Illuminate\Support\Facades\Route;

//--- Home
Route::get('/', 'PageController@index');

Route::get('/arma3sync', function () {
    return redirect('https://docs.google.com/document/d/1i-LzCJE0l_7PtOj8WU717mmmzX1U2KaaNGEnj0KzkIw/view');
});

//--- Shared Missions
Route::get('/share/{mission}', 'ShareController@show');
Route::get('/share/{mission}/{panel}', 'SharePanelController@show');
Route::get('/share/{mission}/briefing/{faction}', 'ShareBriefingController@show');

//--- Authentication
Route::get('/auth/redirect', 'Auth\DiscordController@redirect');
Route::get('/auth/callback', 'Auth\DiscordController@callback');

//--- Public Applications
Route::get('/join/acknowledged', 'PublicJoinController@acknowledged');
Route::resource('join', 'PublicJoinController', [
    'only' => ['index', 'store', 'create']
]);

//--- Media
Route::resource('media', 'MediaController');
Route::post('/media/delete', 'MediaController@deletePhoto');

//--- Roster
Route::get('/roster', 'PageController@roster');

Route::group(['middleware' => 'can:view-applications'], function () {
    // Route::get('/hub/applications/transfer', 'JoinController@transferOldRecords');
    Route::get('/hub/applications/api/items', 'Join\JoinController@items');
    Route::get('/hub/applications/show/{jr}', 'Join\JoinController@show');

    Route::post('/hub/applications/api/send-email', 'Join\JoinController@email');
    Route::get('/hub/applications/api/email-submissions', 'Join\JoinController@emailSubmissions');

    // Statuses
    Route::post('/hub/applications/api/status', 'Join\JoinStatusController@store');
    Route::put('/hub/applications/api/{jr}/status', 'Join\JoinStatusController@update');
    Route::get('/hub/applications/api/{jr}/status', 'Join\JoinStatusController@show');

    Route::get('/hub/applications/{status}', 'Join\JoinController@index');

    Route::resource('/hub/applications', 'Join\JoinController');
});

Route::group(['middleware' => 'can:manage-applications'], function () {
    Route::resource('/hub/applications/api/emails', 'Join\EmailTemplateController');
});

Route::group(['middleware' => 'can:manage-operations'], function () {
    Route::get('/hub/operations', 'Missions\OperationController@index');
    Route::resource('/api/operations', 'API\OperationController');
    Route::resource('/api/operations/missions', 'API\OperationMissionController');
});

//--- Missions
Route::group(['middleware' => 'can:access-hub'], function () {
    Route::controller(MissionTagController::class)->group(function () {
        Route::get('/hub/missions/tags', 'allTags');
        Route::get('/hub/missions/{mission}/tags', 'index');
        Route::post('/hub/missions/{mission}/tags', 'store');
        Route::delete('/hub/missions/{mission}/tags', 'destroy');
        Route::get('/hub/missions/search', 'search');
        Route::get('/hub/missions/modes', 'modes');
    });

    Route::controller(UserController::class)->group(function () {
        Route::get('/hub/users', 'index');
        Route::get('/hub/users/search', 'search');
    });

    Route::controller(MediaController::class)->group(function () {
        Route::post('/hub/missions/media/add-photo', 'uploadPhoto');
        Route::post('/hub/missions/media/delete-photo', 'deletePhoto');
        Route::post('/hub/missions/media/add-video', 'addVideo');
        Route::post('/hub/missions/media/delete-video', 'removeVideo');
    });

    Route::controller(OperationController::class)->group(function () {
        Route::post('/hub/missions/operations/remove-mission', 'removeMission');
        Route::post('/hub/missions/operations/add-mission', 'addMission');
        Route::post('/hub/missions/operations/create-operation', 'create');
        Route::post('/hub/missions/operations/delete-operation', 'destroy');
    });

    // Mission Comments
    Route::resource('/hub/missions/comments', 'Missions\CommentController', [
    'except' => ['create', 'show', 'update']
    ]);

    Route::controller(MissionController::class)->group(function () {
        // Mission Briefings
        Route::post('/hub/missions/briefing', 'briefing');
        Route::post('/hub/missions/briefing/update', 'setBriefingLock');
        // Mission ORBAT
        Route::post('/hub/missions/orbat', 'orbat');
        // Missions
        Route::get('/hub/missions/{mission}/delete', 'destroy');
        Route::post('/hub/missions/{mission}/update', 'update');
        Route::post('/hub/missions/{mission}/set-verification', 'updateVerification');
        // Download
        Route::get('/hub/missions/{mission}/download', 'download');
        // Panels
        Route::get('/hub/missions/{mission}/{panel}', 'panel');
    });

    // Notes
    Route::resource('/hub/missions/{mission}/notes', 'Missions\NoteController');

    Route::resource('/hub/missions', 'Missions\MissionController', [
    'except' => ['create', 'edit']
    ]);
    
    Route::get('/hub/settings/avatar-sync', 'Users\SettingsController@avatarSync');
    Route::resource('/hub/settings', 'Users\SettingsController');

    Route::get('/hub/guides', function () {
        return view('guides.index');
    });

    // Hub Index
    Route::resource('/hub', 'HubController', [
    'only' => ['index']
    ]);

    Route::get('/tokens/create', function (Request $request) {
        auth()->user()->tokens()->delete();
        $token = auth()->user()->createToken('api_token');

        return ['token' => $token->plainTextToken];
    });
});
