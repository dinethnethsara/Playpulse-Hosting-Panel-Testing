<?php

return [
	/*
	|--------------------------------------------------------------------------
	| Plugin Settings
	|--------------------------------------------------------------------------
	|
	| These are the default settings for the example plugin.
	|
	*/
	'enabled' => true,
	'version' => '1.0.0',
	'settings' => [
		'example_setting' => 'default_value'
	],

	/*
	|--------------------------------------------------------------------------
	| Plugin Features
	|--------------------------------------------------------------------------
	|
	| Configure which features of the plugin are enabled.
	|
	*/
	'features' => [
		'admin_interface' => true,
		'api_endpoints' => true,
	],
];