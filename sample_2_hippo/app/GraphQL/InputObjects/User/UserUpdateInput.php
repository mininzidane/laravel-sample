<?php

namespace App\GraphQL\InputObjects\User;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\UserGraphQLType;
use GraphQL\Type\Definition\Type;

class UserUpdateInput extends HippoInputType
{
	protected $attributes = [
		"name" => "userUpdateInput",
		"description" => "The input object for updating a user",
	];

	protected $graphQLType = UserGraphQLType::class;

	protected $inputObject = true;

	/**
	 * @return array[]
	 * @throws SubdomainNotConfiguredException
	 */
	public function fields(): array
	{
		return [
			"username" => [
				"type" => Type::string(),
				"description" => 'The user\'s username',
				"rules" => ["required"],
			],
			"firstName" => [
				"type" => Type::string(),
				"description" => 'The user\'s first name',
				"alias" => "first_name",
			],
			"lastName" => [
				"type" => Type::string(),
				"description" => 'The user\'s last name',
				"alias" => "last_name",
				"rules" => ["required"],
			],
			"degree" => [
				"type" => Type::string(),
				"description" => "The degree held by this user",
			],
			"specialty" => [
				"type" => Type::string(),
				"description" => 'The user\'s medical specialty',
			],
			"license" => [
				"type" => Type::string(),
				"description" => 'The user\'s license number',
			],
			"ein" => [
				"type" => Type::string(),
				"description" => "The EIN for the user",
			],
			"dea" => [
				"type" => Type::string(),
				"description" => "The DEA number for the user",
			],
			"active" => [
				"type" => Type::boolean(),
				"description" => "Whether or not the user is active",
			],
			"emailVerified" => [
				"type" => Type::boolean(),
				"description" =>
					"Indicates whether the user's email address has been verified",
				"alias" => "email_verified",
			],
			"lastLocationId" => [
				"type" => Type::int(),
				"description" => "The last location the user logged into.",
				"alias" => "last_location_id",
			],
			"clientedUsername" => [
				"type" => Type::string(),
				"description" => "The user's ClientEd username",
				"alias" => "cliented_username",
			],
			"clientedPassword" => [
				"type" => Type::string(),
				"description" => "The user's ClientEd password",
				"alias" => "cliented_password",
			],
			"locations" => [
				"type" => Type::listOf(Type::int()),
				"description" => "The locations granted to the user.",
			],
			"roles" => [
				"type" => Type::listOf(Type::int()),
				"description" => "The roles assigned to the user.",
			],
		];
	}

	public function rules(array $args = []): array
	{
		$subdomain = $this->connectToSubdomain();

		return [
			"username" => function ($args) use ($subdomain) {
				return [
					"max:191",
					"email",
					"unique:{$subdomain}App\Models\User,username,{$args["id"]},id",
				];
			},
			"firstName" => ["max:50"],
			"lastName" => ["max:50"],
			"degree" => [
				"max:20",
				"exists:{$subdomain}App\Models\Degree,degree",
			],
			"specialty" => ["max:20"],
			"license" => ["max:50"],
			"ein" => ["max:50"],
			"dea" => ["max:50"],
			"lastLocationId" => ["exists:{$subdomain}App\Models\Location,id"],
			"locations" => ["exists:{$subdomain}App\Models\Location,id"],
			"roles" => ["exists:{$subdomain}Spatie\Permission\Models\Role,id"],
		];
	}
}
