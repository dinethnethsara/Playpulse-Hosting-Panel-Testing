<?php

namespace PlayPulse\Services;

use PlayPulse\Models\Server;
use PlayPulse\Models\Node;
use PlayPulse\Models\Location;
use Carbon\Carbon;

class AutoScalingService
{
	protected $monitoringService;
	protected $thresholds = [
		'scale_up' => [
			'cpu' => 80,
			'memory' => 85,
			'player_count' => 90
		],
		'scale_down' => [
			'cpu' => 30,
			'memory' => 40,
			'player_count' => 30
		]
	];

	public function __construct(MonitoringService $monitoringService)
	{
		$this->monitoringService = $monitoringService;
	}

	public function evaluateServer(Server $server)
	{
		$metrics = $this->monitoringService->collectServerMetrics($server);
		
		if ($this->shouldScaleUp($metrics)) {
			return $this->scaleUp($server);
		}
		
		if ($this->shouldScaleDown($metrics)) {
			return $this->scaleDown($server);
		}

		return null;
	}

	public function optimizeNodeAllocation(Location $location)
	{
		$nodes = $location->getActiveNodes();
		$serverDistribution = $this->calculateOptimalDistribution($nodes);
		
		foreach ($serverDistribution as $nodeId => $servers) {
			$this->rebalanceServers($nodeId, $servers);
		}
	}

	protected function shouldScaleUp($metrics)
	{
		foreach ($this->thresholds['scale_up'] as $metric => $threshold) {
			if (isset($metrics["{$metric}_usage"]) && $metrics["{$metric}_usage"] >= $threshold) {
				return true;
			}
		}
		return false;
	}

	protected function shouldScaleDown($metrics)
	{
		foreach ($this->thresholds['scale_down'] as $metric => $threshold) {
			if (isset($metrics["{$metric}_usage"]) && $metrics["{$metric}_usage"] <= $threshold) {
				return true;
			}
		}
		return false;
	}

	protected function scaleUp(Server $server)
	{
		$newResources = [
			'cpu' => $server->cpu * 1.5,
			'memory' => $server->memory * 1.5,
			'disk' => $server->disk
		];

		return $this->updateServerResources($server, $newResources);
	}

	protected function scaleDown(Server $server)
	{
		$newResources = [
			'cpu' => max($server->cpu * 0.7, $server->initial_cpu),
			'memory' => max($server->memory * 0.7, $server->initial_memory),
			'disk' => $server->disk
		];

		return $this->updateServerResources($server, $newResources);
	}

	protected function updateServerResources(Server $server, array $resources)
	{
		$server->update($resources);
		return $server;
	}

	protected function calculateOptimalDistribution($nodes)
	{
		// Implement load balancing algorithm
		return [];
	}

	protected function rebalanceServers($nodeId, $servers)
	{
		// Implement server migration logic
	}
}