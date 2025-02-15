<?php

namespace PlayPulse\Plugins\ExamplePlugin;

use Illuminate\Support\ServiceProvider;

class ExamplePluginServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->mergeConfigFrom(
			__DIR__.'/../config/example-plugin.php', 'example-plugin'
		);
	}

	public function boot()
	{
		// Register routes
		$this->loadRoutesFrom(__DIR__.'/../routes/web.php');

		// Register views
		$this->loadViewsFrom(__DIR__.'/../resources/views', 'example-plugin');

		// Register migrations
		$this->loadMigrationsFrom(__DIR__.'/../database/migrations');

		// Register commands
		if ($this->app->runningInConsole()) {
			$this->commands([
				Commands\ExampleCommand::class,
			]);
		}

		// Add menu items
		$this->app['events']->listen('panel.menu.admin', function ($menu) {
			$menu->addItem([
				'name' => 'Example Plugin',
				'url' => '/admin/example-plugin',
				'icon' => 'fa fa-puzzle-piece'
			]);
		});
	}
}