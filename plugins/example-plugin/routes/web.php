<?php

use Illuminate\Support\Facades\Route;
use PlayPulse\Plugins\ExamplePlugin\Http\Controllers\ExampleController;

Route::group(['middleware' => ['web', 'admin'], 'prefix' => 'admin/example-plugin'], function () {
	Route::get('/', [ExampleController::class, 'index'])->name('example-plugin.index');
	Route::post('/settings', [ExampleController::class, 'updateSettings'])->name('example-plugin.settings.update');
	Route::get('/status', [ExampleController::class, 'status'])->name('example-plugin.status');
});