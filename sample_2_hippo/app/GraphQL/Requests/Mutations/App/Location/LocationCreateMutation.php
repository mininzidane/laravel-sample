<?php

namespace App\GraphQL\Requests\Mutations\App\Location;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use App\Models\Location;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Contracts\Auth\Authenticatable;
use Rebing\GraphQL\Support\Facades\GraphQL;

class LocationCreateMutation extends LocationMutation
{
	protected $model = Location::class;

	protected $permissionName = "Locations: Create";

	protected $attributes = [
		"name" => "LocationCreateMutation",
	];

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type("LocationInput"),
			],
		];
	}

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.name.required" => "Location name is required",
			"imput.name.max" =>
				"Location name must be smaller than 100 characters",
			"input.address1.max" =>
				"Address1 must be smaller than 100 characters",
			"input.address2.max" =>
				"Address2 must be smaller than 100 characters",
			"input.address3.max" =>
				"Address3 must be smaller than 100 characters",
			"input.city.max" => "City must be smaller than 70 characters",
			"input.zip.max" => "Zip must be smaller than 16 characters",
			"input.phone1.max" => "Phone1 must be smaller than 16 characters",
			"input.phone2.max" => "Phone2 must be smaller than 16 characters",
			"input.phone3.max" => "Phone3 must be smaller than 16 characters",
			"input.fax.max" => "Fax must be smaller than 16 characters",
			"input.email.max" => "Email must be smaller than 255 characters",
			"input.timezone.required" => "The timezone is required",
			"input.timezone.gte" => "A valid timezone is required",
		];
	}

	public function rules(array $args = []): array
	{
		return [
			"input.name" => ["required", "max:100"],
			"input.address1" => ["max:100"],
			"input.address2" => ["max:100"],
			"input.address3" => ["max:100"],
			"input.city" => ["max:70"],
			"input.zip" => ["max:16"],
			"input.phone1" => ["max:16"],
			"input.phone2" => ["max:16"],
			"input.phone3" => ["max:16"],
			"input.fax" => ["max: 16"],
			"input.email" => ["max:255"],
			"input.timezone" => ["required", "gte:1"],
		];
	}

	public function resolveTransaction(
		$root,
		$args,
		$context,
		ResolveInfo $resolveInfo,
		Closure $getSelectFields
	) {
		$modelInstance = new $this->model();
		$modelInstance->setConnection($this->subdomainName);

		if (
			isset($args["input"]["primary"]) &&
			$args["input"]["primary"] === true
		) {
			$this->model::on($this->subdomainName)->update(["primary" => 0]);
		}

		$this->updateLocationEmail();

		$modelInstance->fill($this->args["input"])->save();
		$this->affectedId = $modelInstance->getKey();
		$primaryKey = $modelInstance->getPrimaryKey();

		/*---location create settings---*/
		if (isset($args["input"]["auto_save"])) {
			$modelInstance->auto_save = $args["input"]["auto_save"];
		}
		if (isset($args["input"]["antech_active"])) {
			$modelInstance->antech_active = $args["input"]["antech_active"];
		}
		if (isset($args["input"]["antech_account_id"])) {
			$modelInstance->antech_account_id =
				$args["input"]["antech_account_id"];
		}
		if (isset($args["input"]["antech_clinic_id"])) {
			$modelInstance->antech_clinic_id =
				$args["input"]["antech_clinic_id"];
		}
		if (isset($args["input"]["antech_username"])) {
			$modelInstance->antech_username = $args["input"]["antech_username"];
		}
		if (isset($args["input"]["antech_password"])) {
			$modelInstance->antech_password = $args["input"]["antech_password"];
		}
		if (isset($args["input"]["zoetis_active"])) {
			$modelInstance->zoetis_active = $args["input"]["zoetis_active"];
		}
		if (isset($args["input"]["zoetis_fuse_id"])) {
			$modelInstance->zoetis_fuse_id = $args["input"]["zoetis_fuse_id"];
		}
		/*---------------------*/

		return $this->model
			::on($this->subdomainName)
			->where($primaryKey, $this->affectedId)
			->paginate(1);
	}
}
