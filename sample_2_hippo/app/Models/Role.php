<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
	protected $table = "roles";

	protected $primaryKey = "id";

	public $incrementing = true;

	protected $keyType = "int";

	public $timestamps = true;

	public function userAccessLevels()
	{
		return $this->hasMany(UserAccessLevel::class);
	}

	public function users()
	{
		return $this->belongsToMany(
			User::class,
			"tblUserAccessLevels",
			"access_level",
			"user_id",
		);
	}

	public function accessLevels()
	{
		return $this->belongsTo(AccessLevels::class, "access_level");
	}
}
