<?php

namespace App\GraphQL\InputObjects\PatientAllergy;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\PatientAllergyNoteGraphQLType;
use GraphQL\Type\Definition\Type;

class PatientAllergyNoteInput extends HippoInputType
{
	protected $attributes = [
		"name" => "patientAllergyNoteInput",
		"description" => "A patient allergy to be updated or created",
	];

	protected $graphQLType = PatientAllergyNoteGraphQLType::class;

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
			"note" => [
				"type" => Type::string(),
				"description" => "Description of alert",
				"default" => null,
			],
		];
	}
}
