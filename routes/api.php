<?php

use Illuminate\Support\Facades\Route;
use PlayPulse\Http\Controllers\Api\ServerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
	// Server Management Routes
	Route::prefix('servers')->group(function () {
		Route::post('/', [ServerController::class, 'create']);
		Route::get('/{serverId}/status', [ServerController::class, 'status']);
		Route::patch('/{serverId}/resources', [ServerController::class, 'updateResources']);
		Route::get('/games', [ServerController::class, 'listGames']);
	});

	// Authentication Routes
	Route::post('/auth/login', 'AuthController@login');
	Route::post('/auth/logout', 'AuthController@logout');
	Route::post('/auth/refresh', 'AuthController@refresh');

	// User Management Routes
	Route::apiResource('users', 'UserController');

	// Plugin Routes
	Route::prefix('plugins')->group(function () {
		Route::get('/', 'PluginController@index');
		Route::post('/install', 'PluginController@install');
		Route::delete('/{plugin}', 'PluginController@uninstall');
		Route::patch('/{plugin}', 'PluginController@update');
	});
});

// Health Check Route
Route::get('/health', function () {
	return response()->json(['status' => 'healthy']);
});