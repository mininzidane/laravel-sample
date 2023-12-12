<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAccessLevel extends Model
{
	use HasFactory;

	protected $table = "tblUserAccessLevels";

	protected $primaryKey = ["user_id", "access_level"];

	public $incrementing = false;

	protected $keyType = "string";

	public $timestamps = true;

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function accessLevels()
	{
		return $this->belongsTo(AccessLevels::class, "access_level");
	}

	public function roles()
	{
		return $this->hasManyThrough(
			Role::class,
			AccessLevels::class,
			"access_level",
			"id",
			"access_level",
			"role_id",
		);
	}
}
