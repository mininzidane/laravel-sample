<?php
namespace Step\Api;

abstract class UserStep extends \ApiTester
{
	public function setToken()
	{
		$I = $this;

		$token = null;

		$I->haveHttpHeader("Authorization", "Bearer " . $token);
		$I->haveHttpHeader("Content-Type", "application/json");
	}
}
