<?php

namespace PlayPulse\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{
	use HasFactory;

	protected $fillable = [
		'short',
		'long',
		'region',
		'country',
		'city',
		'timezone',
		'status',
		'network_capacity',
		'ddos_protection_provider',
		'latency_test_url',
		'backup_enabled',
		'auto_scaling_group',
		'network_speed',
		'uplink_redundancy',
		'power_redundancy'
	];

	protected $casts = [
		'backup_enabled' => 'boolean',
		'network_speed' => 'integer',
		'uplink_redundancy' => 'boolean',
		'power_redundancy' => 'boolean',
		'metrics' => 'array',
		'configuration' => 'array'
	];

	public function nodes()
	{
		return $this->hasMany(Node::class);
	}

	public function getActiveNodes()
	{
		return $this->nodes()->where('status', 'online')
			->where('maintenance_mode', false)
			->get();
	}

	public function getTotalResources()
	{
		return [
			'memory' => $this->nodes()->sum('memory'),
			'disk' => $this->nodes()->sum('disk'),
			'cpu_cores' => $this->nodes()->sum('cpu_cores')
		];
	}

	public function getAvailableResources()
	{
		$total = $this->getTotalResources();
		$used = [
			'memory' => $this->nodes()->join('servers', 'nodes.id', '=', 'servers.node_id')
				->sum('servers.memory'),
			'disk' => $this->nodes()->join('servers', 'nodes.id', '=', 'servers.node_id')
				->sum('servers.disk'),
			'cpu' => $this->nodes()->join('servers', 'nodes.id', '=', 'servers.node_id')
				->sum('servers.cpu')
		];

		return [
			'memory' => $total['memory'] - $used['memory'],
			'disk' => $total['disk'] - $used['disk'],
			'cpu' => ($total['cpu_cores'] * 100) - $used['cpu']
		];
	}

	public function getLatency()
	{
		// Implement latency testing logic
		return 0;
	}

	public function isViable()
	{
		return $this->status === 'active' && $this->getActiveNodes()->count() > 0;
	}
}