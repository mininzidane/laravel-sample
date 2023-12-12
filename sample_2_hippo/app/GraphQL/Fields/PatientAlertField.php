<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\PatientAlertGraphQLType;

class PatientAlertField extends HippoField
{
	protected $graphQLType = PatientAlertGraphQLType::class;
	protected $permissionName = "GraphQL: View Alerts";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Alerts",
	];
}
