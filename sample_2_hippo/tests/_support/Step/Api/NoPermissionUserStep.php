<?php
namespace Step\Api;

class NoPermissionUserStep extends UserStep
{
	public function setToken()
	{
		$I = $this;

		$token = $_ENV["HIPPO_API_TOKEN_WITHOUT_ALL_PERMISSIONS"];

		$I->haveHttpHeader("Authorization", "Bearer " . $token);
		$I->haveHttpHeader("Content-Type", "application/json");
	}
}
