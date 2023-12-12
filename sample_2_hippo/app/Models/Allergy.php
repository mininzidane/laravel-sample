<?php

namespace App\Models;

use App\GraphQL\Types\AllergyGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Allergy
 *
 * @property string $name
 */
class Allergy extends HippoModel
{
	use HasFactory;

	public static $graphQLType = AllergyGraphQLType::class;

	protected $table = "tblAllergies";

	protected $primaryKey = "name";
	public $incrementing = false;
	public $timestamps = false;

	protected $fillable = ["name"];
}
