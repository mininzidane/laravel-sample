<?php

namespace App\GraphQL\InputObjects;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\Types\HippoGraphQLType;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Rebing\GraphQL\Support\InputType;

class HippoInputType extends InputType
{
	protected $requiredFields = [];
	protected $graphQLType = HippoGraphQLType::class;

	protected function addRequiredFieldRules($columns)
	{
		foreach ($this->requiredFields as $requiredField) {
			if (array_key_exists($requiredField, $columns)) {
				$columns[$requiredField]["rules"][] = "required";
			}
		}

		return $columns;
	}

	protected function retrieveFieldsFromGraphQLType()
	{
		if ($this->graphQLType) {
			return (new $this->graphQLType())->fields();
		}

		return [];
	}
	//TODO remove this @josh
	protected function manuallyAddedColumns(): array
	{
		return [];
	}

	public function fields(): array
	{
		$columns = $this->retrieveFieldsFromGraphQLType();

		$columns = array_merge($columns, $this->manuallyAddedColumns());

		$columns = $this->addRequiredFieldRules($columns);

		return $columns;
	}

	public function connectToSubdomain()
	{
		$subdomainName = request()->header("Subdomain");

		if (!$subdomainName) {
			return "";
		}

		$connectionDetails = Config::get("database.connections.hippodb");
		$connectionDetails["database"] = "hippodb_" . $subdomainName;
		$connectionName = "database.connections." . $subdomainName;

		Config::set($connectionName, $connectionDetails);

		try {
			DB::connection($subdomainName)->getPdo();
			return $subdomainName . ".";
		} catch (\Exception $e) {
			error_log($e);
			throw new SubdomainNotConfiguredException($subdomainName);
		}
	}
}
