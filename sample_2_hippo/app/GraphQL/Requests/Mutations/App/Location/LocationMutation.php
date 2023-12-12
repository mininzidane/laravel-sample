<?php

namespace App\GraphQL\Requests\Mutations\App\Location;

use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use App\Models\Location;
use Closure;
use Config;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Postmark\PostmarkAdminClient;
use Rebing\GraphQL\Support\Facades\GraphQL;

class LocationMutation extends AppHippoMutation
{
	protected function updateLocationEmail(string $oldEmail = ""): void
	{
		$hasEmail = true;
		if (
			!$this->args["input"]["email"] ||
			strlen($this->args["input"]["email"]) < 1
		) {
			$hasEmail = false;
		}
		if (!$hasEmail || $this->args["input"]["email"] != $oldEmail) {
			$this->args["input"]["email_identity_verified"] = 0;
			$this->args["input"]["public_domain_email"] = 0;
		}

		if ($hasEmail) {
			$postmarkAdmin = new PostmarkAdminClient(
				Config::get("services.postmark.acctToken"),
			);

			try {
				$result = $postmarkAdmin->createSenderSignature(
					$this->args["input"]["email"],
					$this->args["input"]["name"],
				);
			} catch (\Exception $e) {
				switch ($e->postmarkApiErrorCode) {
					//https://postmarkapp.com/developer/api/overview#error-codes
					case 503: // Public Domain
						$this->args["input"]["public_domain_email"] = true;
						break;
					case 504: // Already Added
						// nothing to do
						break;
					default:
						throw $e;
				}
			}
		}
	}
}
