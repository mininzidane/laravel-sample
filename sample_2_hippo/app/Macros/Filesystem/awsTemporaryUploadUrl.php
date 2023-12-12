<?php
// based on https://mauricius.dev/generate-a-temporary-presigned-upload-url-using-laravel-storage-class/

use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\AwsS3v3\AwsS3Adapter;

FilesystemAdapter::macro("awsTemporaryUploadUrl", function (
	$path,
	$expiration,
	array $options = []
) {
	$adapter = $this->driver->getAdapter();

	if ($adapter instanceof AwsS3Adapter) {
		$client = $adapter->getClient();

		$command = $client->getCommand(
			"PutObject",
			array_merge(
				[
					"Bucket" => $adapter->getBucket(),
					"Key" => $adapter->getPathPrefix() . $path,
				],
				$options,
			),
		);

		$uri = $client->createPresignedRequest($command, $expiration)->getUri();

		// If an explicit base URL has been set on the disk configuration then we will use
		// it as the base URL instead of the default path. This allows the developer to
		// have full control over the base path for this filesystem's generated URLs.
		$url = $this->driver->getConfig()->get("temporary_url");
		if (!is_null($url)) {
			/** @phpstan-ignore-next-line */
			$uri = $this->replaceBaseUrl($uri, $url);
		}

		return (string) $uri;
	}

	throw new RuntimeException(
		"This driver does not support creating temporary upload URLs.",
	);
});
