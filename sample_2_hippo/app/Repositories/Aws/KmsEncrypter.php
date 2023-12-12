<?php
namespace App\Repositories\Aws;

use Aws\Exception\AwsException;
use Aws\Kms\KmsClient;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Encryption\EncryptException;
use Illuminate\Contracts\Encryption\StringEncrypter;
//credit where credit is due
//https://blog.deleu.dev/swapping-laravel-encryption-with-aws-kms/
class KmsEncrypter implements Encrypter, StringEncrypter
{
	private $client;
	private $key;
	private $context;

	public function __construct(KmsClient $client, string $key, array $context)
	{
		$this->context = $context;
		$this->key = $key;
		$this->client = $client;
	}

	public function encrypt($value, $serialize = true)
	{
		try {
			return base64_encode(
				$this->client
					->encrypt([
						"KeyId" => $this->key,
						"Plaintext" => $value,
						"EncryptionContext" => $this->context,
					])
					->get("CiphertextBlob"),
			);
		} catch (AwsException $e) {
			throw new EncryptException($e->getMessage(), $e->getCode(), $e);
		}
	}

	public function decrypt($payload, $unserialize = true)
	{
		try {
			$result = $this->client->decrypt([
				"CiphertextBlob" => base64_decode($payload),
				"EncryptionContext" => $this->context,
			]);

			return $result["Plaintext"];
		} catch (AwsException $e) {
			throw new DecryptException($e->getMessage(), $e->getCode(), $e);
		}
	}

	//passport really really wants this here
	public function getKey()
	{
		return $this->key;
	}

	public function encryptString($value): string
	{
		return $this->encrypt($value, false);
	}

	public function decryptString($value): string
	{
		return $this->decrypt($value, false);
	}
}
