<?php
namespace Step\Api;

class ValidUserStep extends \ApiTester
{
	public function setToken()
	{
		$I = $this;

		$token = $_ENV["HIPPO_API_TOKEN"];

		$I->haveHttpHeader("Authorization", "Bearer " . $token);
		$I->haveHttpHeader("Content-Type", "application/json");
	}
}
