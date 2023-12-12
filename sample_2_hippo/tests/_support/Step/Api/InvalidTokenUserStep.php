<?php
namespace Step\Api;

class InvalidTokenUserStep extends UserStep
{
	public function setToken()
	{
		$I = $this;

		$token = "invalid";

		$I->haveHttpHeader("Authorization", "Bearer " . $token);
		$I->haveHttpHeader("Content-Type", "application/json");
	}
}
