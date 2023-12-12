<?php

namespace App\Providers;

use App\Repositories\Aws\KmsEncrypter;
use Aws\Kms\KmsClient;
use Illuminate\Support\ServiceProvider;
//credit where credit is due
//https://blog.deleu.dev/swapping-laravel-encryption-with-aws-kms/
class EncryptionServiceProvider extends ServiceProvider
{
	/**
	 * Register services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton(KmsEncrypter::class, function () {
			$key = config("services.kms.keyId");

			//encrypt with added stuff so like a name or what not
			$context = config("encryption.context");

			$client = $this->app->make(KmsClient::class);

			return new KmsEncrypter($client, $key, $context ?? []);
		});

		$this->app->alias(KmsEncrypter::class, "encrypter");

		$this->app->alias(
			KmsEncrypter::class,
			\Illuminate\Contracts\Encryption\Encrypter::class,
		);

		$this->app->alias(
			KmsEncrypter::class,
			\Illuminate\Contracts\Encryption\StringEncrypter::class,
		);
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
