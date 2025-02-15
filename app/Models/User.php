<?php

namespace PlayPulse\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
	use HasApiTokens, HasFactory, Notifiable;

	protected $fillable = [
		'name',
		'email',
		'password',
		'role',
		'is_admin',
		'last_login_at',
		'last_login_ip'
	];

	protected $hidden = [
		'password',
		'remember_token',
	];

	protected $casts = [
		'email_verified_at' => 'datetime',
		'is_admin' => 'boolean',
		'permissions' => 'array',
		'last_login_at' => 'datetime'
	];

	public function servers()
	{
		return $this->hasMany(Server::class, 'owner_id');
	}

	public function hasPermission($permission)
	{
		if ($this->is_admin) {
			return true;
		}
		return in_array($permission, $this->permissions ?? []);
	}

	public function isAdmin()
	{
		return $this->is_admin;
	}

	public function updateLastLogin()
	{
		$this->last_login_at = now();
		$this->last_login_ip = request()->ip();
		$this->save();
	}

	protected static function boot()
	{
		parent::boot();

		static::creating(function ($user) {
			if (!isset($user->permissions)) {
				$user->permissions = [];
			}
		});
	}
}