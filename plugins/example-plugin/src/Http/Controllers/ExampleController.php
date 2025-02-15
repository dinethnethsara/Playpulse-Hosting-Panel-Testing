<?php

namespace PlayPulse\Plugins\ExamplePlugin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ExampleController extends Controller
{
	public function index()
	{
		$settings = config('example-plugin.settings');
		return view('example-plugin::index', compact('settings'));
	}

	public function updateSettings(Request $request)
	{
		$validated = $request->validate([
			'example_setting' => 'required|string|max:255'
		]);

		// Update plugin settings
		$config = json_decode(file_get_contents(base_path('plugins/example-plugin/config.json')), true);
		$config['config']['settings'] = array_merge($config['config']['settings'], $validated);
		file_put_contents(
			base_path('plugins/example-plugin/config.json'),
			json_encode($config, JSON_PRETTY_PRINT)
		);

		return redirect()->route('example-plugin.index')
			->with('success', 'Settings updated successfully');
	}

	public function status()
	{
		return response()->json([
			'status' => 'active',
			'version' => config('example-plugin.version'),
			'settings' => config('example-plugin.settings')
		]);
	}
}