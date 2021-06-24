<?php

use Laravel\Socialite\Facades\Socialite;

//--- Home
Route::get('/', 'PageController@index');

Route::get('/media', 'Missions\MediaController@index');
Route::get('/roster', 'PageController@roster');
Route::get('/arma3sync', function () {
    return redirect('https://docs.google.com/document/d/1i-LzCJE0l_7PtOj8WU717mmmzX1U2KaaNGEnj0KzkIw/view');
});

Route::prefix('share')->group(function () {
    Route::get('/{mission}', 'ShareController@show');
    Route::get('/{mission}/{panel}', 'SharePanelController@show');
    Route::get('/{mission}/briefing/{faction}', 'ShareBriefingController@show');
});

Route::prefix('auth')->group(function () {
    Route::get('/redirect', 'Auth\DiscordController@redirect');
    Route::get('/callback', 'Auth\DiscordController@callback');
    Route::get('/steam', 'Auth\SteamController@redirect');
    Route::get('/steamcallback', 'Auth\SteamController@callback');
});

//--- Public Applications
Route::get('/join/acknowledged', 'PublicJoinController@acknowledged');
Route::resource('join', 'PublicJoinController', [
    'only' => ['index', 'store', 'create']
]);

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
//});

Route::group(['middleware' => 'can:manage-applications'], function () {
    Route::resource('/hub/applications/api/emails', 'Join\EmailTemplateController');
});

//--- Missions
Route::group(['middleware' => 'can:access-hub'], function () {
    Route::prefix('hub/operations')->middleware('can:manage-operations')->group(function () {
        Route::get('/', 'Operations\OperationController@index');
        Route::post('/', 'Operations\OperationController@store');
        Route::delete('/{operation}', 'Operations\OperationController@destroy');
        
        Route::post('/{operation}/missions', 'Operations\OperationMissionController@store');
        Route::delete('/{operation}/missions', 'Operations\OperationMissionController@destroy');
    });
    
    // Mission Media
    Route::post('/hub/missions/media/add-photo', 'Missions\MediaController@uploadPhoto');
    Route::post('/hub/missions/media/delete-photo', 'Missions\MediaController@deletePhoto');
    Route::post('/hub/missions/media/add-video', 'Missions\MediaController@addVideo');
    Route::post('/hub/missions/media/delete-video', 'Missions\MediaController@removeVideo');

    // Mission Comments
    Route::resource('/hub/missions/comments', 'Missions\CommentController', [
        'except' => ['create', 'show', 'update']
    ]);

    // Mission Briefings
    Route::post('/hub/missions/briefing', 'Missions\MissionController@briefing');
    Route::post('/hub/missions/briefing/update', 'Missions\MissionController@setBriefingLock');

    // Mission ORBAT
    Route::post('/hub/missions/orbat', 'Missions\MissionController@orbat');

    // Missions
    Route::get('/hub/missions/{mission}/delete', 'Missions\MissionController@destroy');
    Route::post('/hub/missions/{mission}/update', 'Missions\MissionController@update');
    Route::post('/hub/missions/{mission}/set-verification', 'Missions\MissionController@updateVerification');

    // Download
    Route::get('/hub/missions/{mission}/download', 'Missions\MissionController@download');

    // Notes
    Route::resource('/hub/missions/{mission}/notes', 'Missions\NoteController');

    // Panels
    Route::get('/hub/missions/{mission}/{panel}', 'Missions\MissionController@panel');

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

Route::group(['middleware' => 'can:view-users'], function () {
    Route::resource('/hub/users', 'Users\UserController');
});
