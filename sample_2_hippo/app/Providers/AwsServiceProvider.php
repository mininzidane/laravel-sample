<?php

namespace App\Providers;

use Aws\Kms\KmsClient;
use Aws\S3\S3Client;
use Illuminate\Support\ServiceProvider;

class AwsServiceProvider extends ServiceProvider
{
	public function register()
	{
		//$this->registerS3();

		$this->registerKms();
	}

	//    private function registerS3(): void
	//    {
	//        $this->app->bind(S3Client::class, function () {
	//            $region = config('aws.region');
	//
	//            return new S3Client(['region' => $region, 'version' => '2006-03-01']);
	//        });
	//    }

	private function registerKms(): void
	{
		$this->app->bind(KmsClient::class, function () {
			return new KmsClient([
				"version" => "2014-11-01",
				"region" => config("services.kms.region"),
			]);
		});
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
