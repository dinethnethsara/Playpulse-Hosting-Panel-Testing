<?php

return [
	/*
	|--------------------------------------------------------------------------
	| Plugin Directory
	|--------------------------------------------------------------------------
	|
	| This value determines the directory where plugins will be stored.
	|
	*/
	'directory' => env('PLUGIN_DIRECTORY', 'plugins'),

	/*
	|--------------------------------------------------------------------------
	| Auto Update
	|--------------------------------------------------------------------------
	|
	| Enable or disable automatic plugin updates.
	|
	*/
	'auto_update' => env('PLUGIN_AUTO_UPDATE', true),

	/*
	|--------------------------------------------------------------------------
	| Allowed Plugin Types
	|--------------------------------------------------------------------------
	|
	| Define the types of plugins that can be installed.
	|
	*/
	'allowed_types' => [
		'theme',
		'integration',
		'game',
		'authentication',
		'backup',
		'monitoring'
	],

	/*
	|--------------------------------------------------------------------------
	| Plugin Repository
	|--------------------------------------------------------------------------
	|
	| The URL of the official plugin repository.
	|
	*/
	'repository_url' => 'https://plugins.playpulse.com',
];