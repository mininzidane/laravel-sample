<?php

namespace App\GraphQL\Resolvers;

use App\Exceptions\SubdomainNotConfiguredException;
use App\Models\Item;
use Closure;
use Config;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\DB;

class DefaultResolver extends HippoResolver
{
	protected $model;
	protected $root;
	protected $args;
	protected $context;
	protected $resolveInfo;

	public function __construct(
		string $model,
		$root,
		$args,
		$context,
		ResolveInfo $resolveInfo
	) {
		$this->model = $model;
		$this->root = $root;
		$this->args = $args;
		$this->context = $context;
		$this->resolveInfo = $resolveInfo;
	}

	public function getArgs()
	{
		return $this->args;
	}

	public function getQuery(Closure $getSelectFields)
	{
		$primaryKey = "id";
		$subdomainName = $this->args["subdomain"];

		if ($this->model) {
			$modelInstance = new $this->model();
			$primaryKey = $modelInstance->getPrimaryKey();
		}

		$connectionDetails = Config::get("database.connections.hippodb");
		$connectionDetails["database"] = "hippodb_" . $subdomainName;
		$connectionName = "database.connections." . $subdomainName;

		Config::set($connectionName, $connectionDetails);

		try {
			DB::connection($subdomainName)->getPdo();
		} catch (Exception $e) {
			error_log($e);
			throw new SubdomainNotConfiguredException($subdomainName);
		}

		$fields = $getSelectFields();
		$select = $fields->getSelect();

		$model = new $this->model();

		$select = array_diff($select, $model->getAppendedFields());

		$with = $fields->getRelations();

		$instanceQuery = $this->model
			::on($subdomainName)
			->select($select)
			->with($with);

		if (isset($this->args["id"])) {
			$instanceQuery->where(
				$model->getTable() . "." . $primaryKey,
				$this->args["id"],
			);
		}

		if (isset($this->args["limit"]) && !isset($this->args["page"])) {
			$instanceQuery->take($this->args["limit"]);
		}

		return $instanceQuery;
	}
}
