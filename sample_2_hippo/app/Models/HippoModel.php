<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HippoModel extends Model
{
	use HasTimestamps;

	use IsDated;

	public static $graphQLType = "";
	protected $createdAtColumn = "created_at";
	protected $updatedAtColumn = "updated_at";
	protected $guarded = ["id"];
	protected $appends = [];

	public static function getGraphQLType()
	{
		return static::$graphQLType;
	}

	public function getAppendedFields()
	{
		return array_map(function ($value) {
			return $this->table . "." . $value;
		}, $this->appends);
	}

	public function getPrimaryKey()
	{
		return $this->primaryKey;
	}

	public function getSoftDeletingAttribute()
	{
		return in_array(SoftDeletes::class, class_uses($this));
	}

	public function getTimestampsAttribute()
	{
		return in_array(HasTimestamps::class, class_uses($this));
	}

	public function getCreatedAtColumn()
	{
		return $this->createdAtColumn;
	}

	public function getUpdatedAtColumn()
	{
		return $this->updatedAtColumn;
	}
}
