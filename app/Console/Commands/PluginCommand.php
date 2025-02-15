<?php

namespace PlayPulse\Console\Commands;

use Illuminate\Console\Command;
use PlayPulse\Services\PluginManager;

class PluginCommand extends Command
{
	protected $signature = 'plugin 
		{action : Action to perform (install|uninstall|update|list)} 
		{name? : Plugin name or ZIP file path}';

	protected $description = 'Manage Play Pulse Panel plugins';

	protected $pluginManager;

	public function __construct(PluginManager $pluginManager)
	{
		parent::__construct();
		$this->pluginManager = $pluginManager;
	}

	public function handle()
	{
		$action = $this->argument('action');
		$name = $this->argument('name');

		switch ($action) {
			case 'install':
				if (!$name) {
					$this->error('Plugin name or ZIP file path is required for installation');
					return 1;
				}
				return $this->installPlugin($name);

			case 'uninstall':
				if (!$name) {
					$this->error('Plugin name is required for uninstallation');
					return 1;
				}
				return $this->uninstallPlugin($name);

			case 'update':
				if ($name) {
					return $this->updatePlugin($name);
				}
				return $this->updateAllPlugins();

			case 'list':
				return $this->listPlugins();

			default:
				$this->error('Invalid action specified');
				return 1;
		}
	}

	protected function installPlugin($nameOrPath)
	{
		try {
			$this->info("Installing plugin: {$nameOrPath}");
			$this->pluginManager->installPlugin($nameOrPath);
			$this->info('Plugin installed successfully');
			return 0;
		} catch (\Exception $e) {
			$this->error("Failed to install plugin: {$e->getMessage()}");
			return 1;
		}
	}

	protected function uninstallPlugin($name)
	{
		try {
			$this->info("Uninstalling plugin: {$name}");
			$this->pluginManager->uninstallPlugin($name);
			$this->info('Plugin uninstalled successfully');
			return 0;
		} catch (\Exception $e) {
			$this->error("Failed to uninstall plugin: {$e->getMessage()}");
			return 1;
		}
	}

	protected function updatePlugin($name)
	{
		try {
			$this->info("Updating plugin: {$name}");
			$this->pluginManager->updatePlugin($name);
			$this->info('Plugin updated successfully');
			return 0;
		} catch (\Exception $e) {
			$this->error("Failed to update plugin: {$e->getMessage()}");
			return 1;
		}
	}

	protected function updateAllPlugins()
	{
		$plugins = $this->pluginManager->getEnabledPlugins();
		foreach ($plugins as $name => $config) {
			$this->updatePlugin($name);
		}
		return 0;
	}

	protected function listPlugins()
	{
		$plugins = $this->pluginManager->getEnabledPlugins();
		if (empty($plugins)) {
			$this->info('No plugins installed');
			return 0;
		}

		$headers = ['Name', 'Version', 'Author', 'Type'];
		$rows = [];

		foreach ($plugins as $name => $config) {
			$rows[] = [
				$name,
				$config['version'] ?? 'Unknown',
				$config['author'] ?? 'Unknown',
				$config['type'] ?? 'Unknown'
			];
		}

		$this->table($headers, $rows);
		return 0;
	}
}