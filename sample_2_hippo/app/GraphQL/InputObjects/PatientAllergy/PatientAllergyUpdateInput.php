<?php

namespace App\GraphQL\InputObjects\PatientAllergy;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\PatientAllergyGraphQLType;
use GraphQL\Type\Definition\Type;

class PatientAllergyUpdateInput extends HippoInputType
{
	protected $attributes = [
		"name" => "patientAllergyUpdateInput",
		"description" => "A patient allergy to be updated",
	];

	protected $graphQLType = PatientAllergyGraphQLType::class;

	protected $inputObject = true;

	/**
	 * @return array[]
	 * @throws SubdomainNotConfiguredException
	 */
	public function fields(): array
	{
		$subdomainName = $this->connectToSubdomain();

		return [
			"clientId" => [
				"type" => Type::int(),
				"description" => "Description of alert",
				"alias" => "client_id",
				"default" => null,
			],
			"allergy" => [
				"type" => Type::string(),
				"description" => "An allergy",
				"alias" => "allergy",
				"default" => null,
			],
		];
	}
}
