<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\TreatmentSheetTreatmentGraphQLType;

class TreatmentSheetTreatmentField extends HippoField
{
	protected $graphQLType = TreatmentSheetTreatmentGraphQLType::class;
	protected $permissionName = "GraphQL: View Treatment Sheet Treatments";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Treatment Sheet Treatments",
	];
}
