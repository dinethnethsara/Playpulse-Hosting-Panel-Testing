<?php

namespace PlayPulse\Services;

use PlayPulse\Models\Server;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class GameTemplateService
{
	protected $supportedGames = [
		'minecraft' => [
			'java' => [
				'versions' => ['1.8', '1.12', '1.16', '1.17', '1.18', '1.19', '1.20'],
				'types' => ['vanilla', 'spigot', 'paper', 'forge', 'fabric']
			],
			'bedrock' => [
				'versions' => ['latest'],
				'types' => ['vanilla', 'pocketmine']
			]
		],
		'csgo' => [
			'competitive' => [
				'versions' => ['latest'],
				'types' => ['vanilla', 'sourcemod', 'metamod']
			]
		],
		'rust' => [
			'standard' => [
				'versions' => ['latest'],
				'types' => ['vanilla', 'modded', 'staging']
			]
		],
		'ark' => [
			'survival' => [
				'versions' => ['latest'],
				'types' => ['vanilla', 'modded']
			]
		]
	];

	public function getAvailableTemplates()
	{
		return $this->supportedGames;
	}

	public function createServerFromTemplate(array $data)
	{
		$template = $this->getTemplate($data['game'], $data['type'], $data['version']);
		
		return [
			'docker_image' => $template['docker_image'],
			'startup' => $this->generateStartupCommand($template, $data),
			'config_files' => $this->getConfigFiles($template),
			'environment' => $this->getEnvironmentVariables($template),
			'resource_limits' => $this->getResourceLimits($template)
		];
	}

	protected function getTemplate($game, $type, $version)
	{
		$cacheKey = "template:{$game}:{$type}:{$version}";
		
		return Cache::remember($cacheKey, 3600, function () use ($game, $type, $version) {
			return $this->loadTemplate($game, $type, $version);
		});
	}

	protected function loadTemplate($game, $type, $version)
	{
		$path = "templates/{$game}/{$type}/{$version}/template.json";
		if (!Storage::exists($path)) {
			throw new \Exception("Template not found");
		}

		return json_decode(Storage::get($path), true);
	}

	protected function generateStartupCommand($template, $data)
	{
		$command = $template['startup_command'];
		foreach ($data['variables'] ?? [] as $key => $value) {
			$command = str_replace("{{$key}}", $value, $command);
		}
		return $command;
	}

	protected function getConfigFiles($template)
	{
		return array_map(function ($file) {
			return [
				'path' => $file['path'],
				'content' => $this->processConfigTemplate($file['template'])
			];
		}, $template['config_files'] ?? []);
	}

	protected function getEnvironmentVariables($template)
	{
		return $template['environment'] ?? [];
	}

	protected function getResourceLimits($template)
	{
		return [
			'memory' => $template['resources']['memory'] ?? '2048M',
			'cpu' => $template['resources']['cpu'] ?? '100',
			'disk' => $template['resources']['disk'] ?? '10240',
			'io' => $template['resources']['io'] ?? '500'
		];
	}
}