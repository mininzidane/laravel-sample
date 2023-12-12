<?php

namespace App\Extensions\Hashing;

use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Hashing\AbstractHasher;

class SHA1Hasher extends AbstractHasher implements HasherContract
{
	/**
	 * Hash the given value.
	 *
	 * @param string $value
	 * @param array $options
	 * @return string
	 *
	 * @throws \RuntimeException
	 */
	public function make($value, array $options = [])
	{
		return sha1($value . $options["salt"]);
	}

	/**
	 * Check the given plain value against a hash.
	 *
	 * @param string $value
	 * @param string $hashedValue
	 * @param array $options
	 * @return bool
	 *
	 * @throws \RuntimeException
	 */
	public function check($value, $hashedValue, array $options = [])
	{
		return $value === $hashedValue;
	}

	/**
	 * Check if the given hash has been hashed using the given options.
	 *
	 * @param string $hashedValue
	 * @param array $options
	 * @return bool
	 */
	public function needsRehash($hashedValue, array $options = [])
	{
		// Determine possible uses for SHA1
	}
}
