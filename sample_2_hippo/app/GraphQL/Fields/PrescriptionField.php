<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\PrescriptionGraphQLType;

class PrescriptionField extends HippoField
{
	protected $graphQLType = PrescriptionGraphQLType::class;
	protected $permissionName = "GraphQL: View Prescriptions";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Prescriptions",
	];
}
