<?php

namespace App\Models;

use App\GraphQL\Types\LogGraphQLType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Log
 *
 * @property-read int $id
 *
 * @property int $organization_id
 * @property int $location_id
 * @property int $user_id
 * @property int $action_id
 * @property int $affected_id
 * @property string $information
 * @property Carbon $timestamp
 * @property string $screen
 *
 * @property-read LogAction $actions
 * @property-read Organization $organization
 * @property-read Location $location
 * @property-read User $user
 */
class Log extends HippoModel
{
	use HasFactory;

	public static $graphQLType = LogGraphQLType::class;

	protected $table = "tblLog";

	protected $fillable = [
		"organization_id",
		"location_id",
		"user_id",
		"action_id",
		"affected_id",
		"information",
		"timestamp",
		"screen",
	];

	public function __construct(array $attributes = [])
	{
		$this->timestamps = false;

		parent::__construct($attributes);
	}

	public function actions(): BelongsTo
	{
		return $this->belongsTo(LogAction::class, "action_id");
	}

	public function organization(): BelongsTo
	{
		return $this->belongsTo(Organization::class, "organization_id");
	}

	public function location(): BelongsTo
	{
		return $this->belongsTo(Location::class, "location_id");
	}

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class, "user_id");
	}
}
