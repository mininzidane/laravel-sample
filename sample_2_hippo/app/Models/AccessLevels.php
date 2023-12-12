<?php

namespace App\Models;

class AccessLevels extends HippoModel
{
	protected $table = "tblAccessLevels";

	public function userAccessLevels()
	{
		return $this->hasMany(UserAccessLevel::class, "access_level");
	}

	public function roles()
	{
		return $this->hasMany(Role::class, "role_id");
	}

	public function role()
	{
		return $this->hasOne(Role::class, "role_id");
	}
}
