<?php

namespace PlayPulse\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ServerManager
{
	protected $servers = [];
	protected $activeGames = [];

	public function __construct()
	{
		$this->loadServerConfigurations();
	}

	public function createServer($data)
	{
		try {
			// Server creation logic with enhanced resource management
			$serverId = uniqid('srv_');
			$this->servers[$serverId] = array_merge($data, [
				'id' => $serverId,
				'status' => 'creating',
				'resources' => [
					'cpu' => $data['cpu'] ?? '100%',
					'memory' => $data['memory'] ?? '2048M',
					'disk' => $data['disk'] ?? '10G'
				],
				'monitoring' => [
					'enabled' => true,
					'interval' => 30,
					'alerts' => true
				]
			]);
			
			return $serverId;
		} catch (\Exception $e) {
			Log::error("Server creation failed: " . $e->getMessage());
			throw $e;
		}
	}

	public function getServerStatus($serverId)
	{
		return $this->servers[$serverId]['status'] ?? 'not_found';
	}

	public function updateServerResources($serverId, $resources)
	{
		if (isset($this->servers[$serverId])) {
			$this->servers[$serverId]['resources'] = array_merge(
				$this->servers[$serverId]['resources'],
				$resources
			);
			return true;
		}
		return false;
	}

	protected function loadServerConfigurations()
	{
		$this->activeGames = [
			'minecraft' => ['java', 'bedrock'],
			'csgo' => ['competitive', 'casual'],
			'rust' => ['vanilla', 'modded'],
			'ark' => ['standard', 'custom'],
			'valheim' => ['standard']
		];
	}

	public function getAvailableGames()
	{
		return $this->activeGames;
	}

	public function monitorServer($serverId)
	{
		if (!isset($this->servers[$serverId])) {
			return null;
		}

		return [
			'status' => $this->servers[$serverId]['status'],
			'uptime' => Cache::get("server.{$serverId}.uptime"),
			'cpu_usage' => Cache::get("server.{$serverId}.cpu"),
			'memory_usage' => Cache::get("server.{$serverId}.memory"),
			'network' => Cache::get("server.{$serverId}.network"),
			'players' => Cache::get("server.{$serverId}.players")
		];
	}
}