<?php

namespace App\GraphQL\InputObjects\PatientAlert;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\PatientAlertGraphQLType;
use GraphQL\Type\Definition\Type;

class PatientAlertInput extends HippoInputType
{
	protected $attributes = [
		"name" => "patientAlertInput",
		"description" => "A patient alert to be updated or created",
	];

	//protected $requiredFields = ['name', 'description'];
	protected $graphQLType = PatientAlertGraphQLType::class;

	protected $inputObject = true;

	/**
	 * @return array[]
	 * @throws SubdomainNotConfiguredException
	 */
	public function fields(): array
	{
		$subdomainName = $this->connectToSubdomain();

		return [
			"description" => [
				"type" => Type::string(),
				"description" => "Description of alert",
				"default" => null,
			],
			"addedBy" => [
				"type" => Type::int(),
				"description" => "Description of alert",
				"alias" => "added_by",
				"default" => null,
			],
			"patient" => [
				"type" => Type::int(),
				"description" => "Description of alert",
				"alias" => "client_id",
				"default" => null,
			],
			"organization" => [
				"type" => Type::int(),
				"description" => "Description of alert",
				"alias" => "organization_id",
				"default" => null,
			],
		];
	}
}
