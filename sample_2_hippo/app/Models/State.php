<?php

namespace App\Models;

use App\GraphQL\Types\StateGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class State extends HippoModel
{
	use HasName;
	use HasFactory;

	public static $graphQLType = StateGraphQLType::class;

	protected $table = "tblSubRegions";

	protected $fillable = ["region_id", "name", "timezone", "iso", "code"];

	public function __construct(array $attributes = [])
	{
		$this->nameFields = ["name", "code", "iso"];

		parent::__construct($attributes);
	}

	public function owners()
	{
		return $this->hasMany(Owner::class, "state");
	}

	public function locations()
	{
		return $this->hasMany(Location::class, "state");
	}

	public function suppliers()
	{
		return $this->hasMany(Supplier::class);
	}
}
