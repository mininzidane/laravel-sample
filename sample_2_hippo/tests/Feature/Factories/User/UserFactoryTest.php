<?php

declare(strict_types=1);

namespace Tests\Feature\Factories\User;

use App\Models\User;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class UserFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data(): void
	{
		/** @var User $model */
		$model = User::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"username" => $model->username,
			"password" => $model->password,
			"salt" => $model->salt,
			"organization_id" => $model->organization_id,
			"administrator" => $model->administrator,
			"active" => $model->active,
			"degree" => $model->degree,
			"first_name" => $model->first_name,
			"last_name" => $model->last_name,
			"email" => $model->email,
			"specialty" => $model->specialty,
			"phone1" => $model->phone1,
			"phone1_ext" => $model->phone1_ext,
			"drug_alerts_view_mild" => $model->drug_alerts_view_mild,
			"drug_alerts_view_moderate" => $model->drug_alerts_view_moderate,
			"drug_alerts_view_severe" => $model->drug_alerts_view_severe,
			"allergy_alerts_view_mild" => $model->allergy_alerts_view_mild,
			"allergy_alerts_view_moderate" =>
				$model->allergy_alerts_view_moderate,
			"allergy_alerts_view_severe" => $model->allergy_alerts_view_severe,
			"list_appointment_scheduler" => $model->list_appointment_scheduler,
			"enroll_template_store" => $model->enroll_template_store,
			"enroll_patients_phr" => $model->enroll_patients_phr,
			"npi" => $model->npi,
			"ein" => $model->ein,
			"upin" => $model->upin,
			"license" => $model->license,
			"dea" => $model->dea,
			"removed" => $model->removed,
			"first_login" => $model->first_login,
			"sec_question" => $model->sec_question,
			"sec_answer" => $model->sec_answer,
			"agree_terms" => $model->agree_terms,
			"title" => $model->title,
			"organization_role" => $model->organization_role,
			"landing" => $model->landing,
			"last_client" => $model->last_client,
			"last_location_id" => $model->last_location_id,
			"sig_name" => $model->sig_name,
			"hidden" => $model->hidden,
			"email_verified" => $model->email_verified,
			"email_verified_timestamp" => $model->email_verified_timestamp,
			"cliented_username" => $model->cliented_username,
			"cliented_password" => $model->cliented_password,
		]);
	}
}
