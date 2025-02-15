<?php

namespace PlayPulse\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PluginManager
{
	protected $pluginDirectory;
	protected $enabledPlugins = [];

	public function __construct()
	{
		$this->pluginDirectory = base_path(config('plugins.directory', 'plugins'));
		$this->initializePluginDirectory();
	}

	protected function initializePluginDirectory()
	{
		if (!File::exists($this->pluginDirectory)) {
			File::makeDirectory($this->pluginDirectory, 0755, true);
		}
	}

	public function loadPlugins()
	{
		if (!File::exists($this->pluginDirectory)) {
			return;
		}

		$plugins = File::directories($this->pluginDirectory);
		foreach ($plugins as $pluginPath) {
			$this->loadPlugin($pluginPath);
		}
	}

	protected function loadPlugin($path)
	{
		$configFile = $path . '/config.json';
		if (!File::exists($configFile)) {
			Log::warning("Plugin at {$path} is missing config.json");
			return;
		}

		try {
			$config = json_decode(File::get($configFile), true);
			if ($this->validatePluginConfig($config)) {
				$this->enabledPlugins[$config['name']] = $config;
				$this->registerPluginRoutes($path);
				$this->registerPluginProviders($config);
			}
		} catch (\Exception $e) {
			Log::error("Failed to load plugin at {$path}: " . $e->getMessage());
		}
	}

	protected function validatePluginConfig($config)
	{
		return isset($config['name']) && 
			   isset($config['version']) && 
			   isset($config['author']);
	}

	protected function registerPluginRoutes($path)
	{
		$routesFile = $path . '/routes.php';
		if (File::exists($routesFile)) {
			require $routesFile;
		}
	}

	protected function registerPluginProviders($config)
	{
		if (isset($config['providers'])) {
			foreach ($config['providers'] as $provider) {
				app()->register($provider);
			}
		}
	}

	public function getEnabledPlugins()
	{
		return $this->enabledPlugins;
	}

	public function installPlugin($zipFile)
	{
		// Implementation for installing plugins from zip files
	}

	public function uninstallPlugin($pluginName)
	{
		// Implementation for uninstalling plugins
	}

	public function updatePlugin($pluginName)
	{
		// Implementation for updating plugins
	}
}