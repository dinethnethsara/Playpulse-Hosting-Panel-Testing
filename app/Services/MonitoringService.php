<?php

namespace PlayPulse\Services;

use PlayPulse\Models\Server;
use PlayPulse\Models\Node;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

class MonitoringService
{
	protected $metricsRetention = 30; // days
	protected $alertThresholds = [
		'cpu' => 90,
		'memory' => 90,
		'disk' => 90,
		'network' => 1000, // Mbps
		'latency' => 100   // ms
	];

	public function collectServerMetrics(Server $server)
	{
		$metrics = [
			'timestamp' => Carbon::now(),
			'cpu_usage' => $this->getCpuUsage($server),
			'memory_usage' => $this->getMemoryUsage($server),
			'disk_usage' => $this->getDiskUsage($server),
			'network_stats' => $this->getNetworkStats($server),
			'player_count' => $this->getPlayerCount($server),
			'uptime' => $this->getUptime($server),
			'latency' => $this->getLatency($server),
			'performance_score' => $this->calculatePerformanceScore($server)
		];

		$this->storeMetrics($server, $metrics);
		$this->checkAlerts($server, $metrics);
		
		return $metrics;
	}

	public function getNodePerformance(Node $node)
	{
		return [
			'cpu_usage' => $this->getNodeCpuUsage($node),
			'memory_usage' => $this->getNodeMemoryUsage($node),
			'disk_usage' => $this->getNodeDiskUsage($node),
			'network_throughput' => $this->getNodeNetworkThroughput($node),
			'server_count' => $node->servers()->count(),
			'health_status' => $this->getNodeHealthStatus($node)
		];
	}

	protected function storeMetrics(Server $server, array $metrics)
	{
		$key = "server:{$server->id}:metrics";
		Redis::hset($key, Carbon::now()->timestamp, json_encode($metrics));
		Redis::expire($key, $this->metricsRetention * 86400);
	}

	protected function checkAlerts(Server $server, array $metrics)
	{
		foreach ($this->alertThresholds as $metric => $threshold) {
			if (isset($metrics["{$metric}_usage"]) && $metrics["{$metric}_usage"] > $threshold) {
				$this->triggerAlert($server, $metric, $metrics["{$metric}_usage"]);
			}
		}
	}

	protected function triggerAlert(Server $server, $metric, $value)
	{
		// Implement alert notification logic
		event(new ServerAlertTriggered($server, $metric, $value));
	}

	public function getHistoricalMetrics(Server $server, $period = '24h')
	{
		$key = "server:{$server->id}:metrics";
		$start = $this->getStartTime($period);
		return Redis::hgetall($key);
	}

	protected function getStartTime($period)
	{
		return match($period) {
			'1h' => Carbon::now()->subHour(),
			'24h' => Carbon::now()->subDay(),
			'7d' => Carbon::now()->subWeek(),
			'30d' => Carbon::now()->subMonth(),
			default => Carbon::now()->subDay()
		};
	}

	protected function calculatePerformanceScore(Server $server)
	{
		// Implement performance scoring algorithm
		return 100;
	}
}