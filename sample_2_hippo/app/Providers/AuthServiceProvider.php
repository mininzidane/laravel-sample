<?php

namespace App\Providers;

use App\Extensions\Providers\SubdomainEloquentUserProvider;
use App\Models\Passport\AuthCode;
use App\Models\Passport\Client;
use App\Models\Passport\PersonalAccessClient;
use App\Models\Passport\Token;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
	/**
	 * The policy mappings for the application.
	 *
	 * @var array
	 */
	protected $policies = []; // 'App\Model' => 'App\Policies\ModelPolicy',

	/**
	 * Register any authentication / authorization services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->registerPolicies();

		Passport::routes(function ($router) {
			$router->forAuthorization();
			$router->forAccessTokens();
			$router->forTransientTokens();
			$router->forClients();
		});

		Passport::tokensCan([]);

		if (
			request()->hasHeader("referer") &&
			request()->header("referer") !== ""
		) {
			$subdomain = explode(
				".",
				explode("//", request()->header("referer"))[1],
			)[0];
			Passport::cookie("_subdomain_token_" . $subdomain);
		}

		Passport::useTokenModel(Token::class);
		Passport::useClientModel(Client::class);
		Passport::useAuthCodeModel(AuthCode::class);
		Passport::usePersonalAccessClientModel(PersonalAccessClient::class);

		Auth::provider("subdomain_eloquent_users", function (
			$app,
			array $config
		) {
			return new SubdomainEloquentUserProvider($config);
		});
	}
}
