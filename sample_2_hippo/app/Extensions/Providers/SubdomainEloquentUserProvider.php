<?php

namespace App\Extensions\Providers;

use App\Exceptions\SubdomainNotConfiguredException;
use App\Extensions\Hashing\SHA1Hasher;
use App\Models\User;
use Exception;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SubdomainEloquentUserProvider extends EloquentUserProvider
{
	private $config;
	private $subdomain;

	public function __construct($subdomain)
	{
		$this->config = Config::get("auth.providers.subdomain_eloquent_users");
		$this->subdomain = $subdomain;

		$hasher = new SHA1Hasher();

		parent::__construct($hasher, $this->config["model"]);
	}

	/**
	 * Get a new query builder for the model instance.
	 *
	 * @param \Illuminate\Database\Eloquent\Model|null $model
	 * @return \Illuminate\Database\Eloquent\Builder
	 * @throws SubdomainNotConfiguredException
	 */
	protected function newModelQuery($model = null)
	{
		if (is_array($this->subdomain)) {
			$subdomain = $this->subdomain["subdomain"];
		} else {
			$subdomain = $this->subdomain;
		}

		$this->connectToSubdomain($subdomain);

		return is_null($model)
			? $this->createModel()
				->setConnection($subdomain)
				->newQuery()
			: $model->setConnection($subdomain)->newQuery();
	}

	/**
	 * Validate an active user against the given credentials
	 *
	 * @param \Illuminate\Contracts\Auth\Authenticatable $user
	 * @param array $credentials
	 * @return bool
	 */
	public function validateCredentials(UserContract $user, array $credentials)
	{
		if (!$user->active) {
			return false;
		}

		$plain = $credentials["password"];

		$hash = $this->hasher->make($plain, ["salt" => $user->getAuthSalt()]);

		return $this->hasher->check($hash, $user->getAuthPassword());
	}

	public function connectToSubdomain($subdomain)
	{
		$connectionDetails = Config::get("database.connections.hippodb");
		$connectionDetails["database"] = "hippodb_" . $subdomain;
		$connectionName = "database.connections." . $subdomain;

		Config::set($connectionName, $connectionDetails);

		try {
			DB::connection($subdomain)->getPdo();
		} catch (Exception $e) {
			error_log($e);
			throw new SubdomainNotConfiguredException($subdomain);
		}
	}
}
