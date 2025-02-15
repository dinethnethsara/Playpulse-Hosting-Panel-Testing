<?php

namespace PlayPulse\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Node extends Model
{
	use HasFactory;

	protected $fillable = [
		'name',
		'location_id',
		'fqdn',
		'scheme',
		'behind_proxy',
		'maintenance_mode',
		'memory',
		'memory_overallocate',
		'disk',
		'disk_overallocate',
		'upload_size',
		'daemon_token',
		'daemon_token_id',
		'status',
		'cpu_cores',
		'network_speed',
		'network_mode',
		'virtualization',
		'auto_scaling_enabled',
		'ddos_protection'
	];

	protected $casts = [
		'behind_proxy' => 'boolean',
		'maintenance_mode' => 'boolean',
		'memory_overallocate' => 'integer',
		'disk_overallocate' => 'integer',
		'auto_scaling_enabled' => 'boolean',
		'ddos_protection' => 'boolean',
		'configuration' => 'array',
		'metrics' => 'array'
	];

	protected $hidden = [
		'daemon_token',
		'daemon_token_id'
	];

	public function location()
	{
		return $this->belongsTo(Location::class);
	}

	public function servers()
	{
		return $this->hasMany(Server::class);
	}

	public function allocations()
	{
		return $this->hasMany(Allocation::class);
	}

	public function isViable()
	{
		return !$this->maintenance_mode && $this->status === 'online';
	}

	public function getAvailableResources()
	{
		return [
			'memory' => $this->getAvailableMemory(),
			'disk' => $this->getAvailableDisk(),
			'cpu' => $this->getAvailableCpu()
		];
	}

	protected function getAvailableMemory()
	{
		$used = $this->servers()->sum('memory');
		return $this->memory - $used;
	}

	protected function getAvailableDisk()
	{
		$used = $this->servers()->sum('disk');
		return $this->disk - $used;
	}

	protected function getAvailableCpu()
	{
		return $this->cpu_cores * 100 - $this->servers()->sum('cpu');
	}
}