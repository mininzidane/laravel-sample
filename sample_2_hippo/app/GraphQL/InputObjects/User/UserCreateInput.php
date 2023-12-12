<?php

namespace App\GraphQL\InputObjects\User;

use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\UserGraphQLType;
use GraphQL\Type\Definition\Type;

class UserCreateInput extends HippoInputType
{
	protected $attributes = [
		"name" => "userCreateInput",
		"description" => "The input object for creating a user",
	];

	protected $graphQLType = UserGraphQLType::class;

	public function fields(): array
	{
		$subdomain = $this->connectToSubdomain();

		return [
			"username" => [
				"type" => Type::string(),
				"description" => 'The user\'s username',
				"rules" => [
					"required",
					"max:191",
					"email",
					"unique:{$subdomain}App\Models\User,username",
				],
			],
			"firstName" => [
				"type" => Type::string(),
				"description" => 'The user\'s first name',
				"alias" => "first_name",
				"default" => null,
				"rules" => ["max:50"],
			],
			"lastName" => [
				"type" => Type::string(),
				"description" => 'The user\'s last name',
				"alias" => "last_name",
				"default" => "",
				"rules" => ["max:50", "required"],
			],
			"degree" => [
				"type" => Type::string(),
				"description" => "The degree held by this user",
				"default" => null,
				"rules" => [
					"max:20",
					"exists:{$subdomain}App\Models\Degree,degree",
				],
			],
			"specialty" => [
				"type" => Type::string(),
				"description" => 'The user\'s medical specialty',
				"default" => null,
				"rules" => ["max:20"],
			],
			"license" => [
				"type" => Type::string(),
				"description" => 'The user\'s license number',
				"default" => null,
				"rules" => ["max:50"],
			],
			"ein" => [
				"type" => Type::string(),
				"description" => "The EIN for the user",
				"default" => null,
				"rules" => ["max:50"],
			],
			"dea" => [
				"type" => Type::string(),
				"description" => "The DEA number for the user",
				"default" => null,
				"rules" => ["max:50"],
			],
			"active" => [
				"type" => Type::boolean(),
				"description" => "Whether or not the user is active",
				"default" => true,
			],
			"organization" => [
				"type" => Type::int(),
				"description" =>
					"The id of the organization this user belongs to",
				"relation" => true,
				"alias" => "organization_id",
				"rules" => [
					"required",
					"exists:{$subdomain}App\Models\Organization,id",
				],
			],
			"locations" => [
				"type" => Type::listOf(Type::int()),
				"description" => "The locations granted to the user.",
				"rules" => [
					"required",
					"exists:{$subdomain}App\Models\Location,id",
				],
			],
			"roles" => [
				"type" => Type::listOf(Type::int()),
				"description" => "The roles assigned to the user.",
				"rules" => [
					"required",
					"exists:{$subdomain}Spatie\Permission\Models\Role,id",
				],
			],
		];
	}
}
