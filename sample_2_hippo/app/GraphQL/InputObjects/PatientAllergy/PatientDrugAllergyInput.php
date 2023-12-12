<?php

namespace App\GraphQL\InputObjects\PatientAllergy;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\PatientDrugAllergyGraphQLType;
use GraphQL\Type\Definition\Type;

class PatientDrugAllergyInput extends HippoInputType
{
	protected $attributes = [
		"name" => "patientDrugAllergyInput",
		"description" => "A patient allergy to be updated or created",
	];

	protected $graphQLType = PatientDrugAllergyGraphQLType::class;

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
				"description" => "a list of allergies",
				"alias" => "allergy",
				"default" => null,
			],
		];
	}
}
