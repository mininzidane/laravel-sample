<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MacrosServiceProvider extends ServiceProvider
{
	/**
	 * Register services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
		require_once __DIR__ . "/../Macros/QueryBuilder/whereDateBetween.php";
		require_once __DIR__ . "/../Macros/QueryBuilder/whereLike.php";
		require_once __DIR__ . "/../Macros/QueryBuilder/orWhereLike.php";
		require_once __DIR__ . "/../Macros/QueryBuilder/search.php";

		require_once __DIR__ . "/../Macros/Eloquent/whereLike.php";
		require_once __DIR__ . "/../Macros/Eloquent/orWhereLike.php";
		require_once __DIR__ . "/../Macros/Eloquent/search.php";

		require_once __DIR__ .
			"/../Macros/Filesystem/awsTemporaryUploadUrl.php";
	}

	/**
	 * Bootstrap services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//
	}
}
