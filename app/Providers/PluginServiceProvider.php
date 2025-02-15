<?php

namespace PlayPulse\Providers;

use Illuminate\Support\ServiceProvider;
use PlayPulse\Services\PluginManager;

class PluginServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->singleton(PluginManager::class, function ($app) {
			return new PluginManager();
		});

		$this->mergeConfigFrom(
			__DIR__.'/../../config/plugins.php', 'plugins'
		);
	}

	public function boot()
	{
		$this->publishes([
			__DIR__.'/../../config/plugins.php' => config_path('plugins.php'),
		], 'playpulse-config');

		if ($this->app->runningInConsole()) {
			$this->commands([
				// Register plugin-related commands here
			]);
		}

		$pluginManager = $this->app->make(PluginManager::class);
		$pluginManager->loadPlugins();
	}
}