<?php

namespace PlayPulse\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Server extends Model
{
	use HasFactory;

	protected $fillable = [
		'name',
		'owner_id',
		'game_type',
		'node_id',
		'allocation_id',
		'cpu',
		'memory',
		'disk',
		'status',
		'installation_status',
		'suspended'
	];

	protected $casts = [
		'suspended' => 'boolean',
		'resources' => 'array',
		'configuration' => 'array',
		'installed_at' => 'datetime',
		'last_online' => 'datetime'
	];

	protected $attributes = [
		'status' => 'installing',
		'suspended' => false
	];

	public function owner()
	{
		return $this->belongsTo(User::class, 'owner_id');
	}

	public function node()
	{
		return $this->belongsTo(Node::class);
	}

	public function allocation()
	{
		return $this->belongsTo(Allocation::class);
	}

	public function backups()
	{
		return $this->hasMany(Backup::class);
	}

	public function isOnline()
	{
		return $this->status === 'running';
	}

	public function isSuspended()
	{
		return $this->suspended;
	}

	public function suspend()
	{
		$this->suspended = true;
		$this->status = 'suspended';
		$this->save();
	}

	public function unsuspend()
	{
		$this->suspended = false;
		$this->status = 'offline';
		$this->save();
	}
}