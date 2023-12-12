<?php

namespace App\GraphQL\InputObjects\PatientAllergy;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\PatientAllergyGraphQLType;
use GraphQL\Type\Definition\Type;

class PatientAllergyCreateInput extends HippoInputType
{
	protected $attributes = [
		"name" => "patientAllergyCreateInput",
		"description" => "A patient allergy to be created",
	];

	//protected $requiredFields = ['name', 'description'];
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
