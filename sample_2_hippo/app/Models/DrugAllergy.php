<?php

namespace App\Models;

use App\GraphQL\Types\DrugAllergyGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DrugAllergy extends HippoModel
{
	use HasFactory;

	public static $graphQLType = DrugAllergyGraphQLType::class;

	protected $table = "tblDrugAllergies";
	protected $primaryKey = "name";
	public $incrementing = false;
	public $timestamps = false;

	protected $fillable = ["name"];

	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
	}
}
