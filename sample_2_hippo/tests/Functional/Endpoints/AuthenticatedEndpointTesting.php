<?php
namespace Tests\Functional\Endpoints;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;
use Tests\TestCase;

class AuthenticatedEndpointTesting extends PassportSetupTestCase
{
	//By using the PassportSetupTestCase setup we can write authenticated testing
	//Add the role you want to test to the protected $role section
	use TruncateDatabase;
	protected $role = "super_user";

	/**
	 * Test if authenticated user can access authenticated endpoints
	 *
	 * @return void
	 */
	public function test_authenticated_user_can_access_authenticated_endpoints()
	{
		$this->markTestSkipped("Write me some testing");
	}
}
