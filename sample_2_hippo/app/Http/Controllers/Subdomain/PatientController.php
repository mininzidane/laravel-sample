<?php

namespace App\Http\Controllers\Subdomain;

use App\Models\Patient;
use App\Models\PatientImage;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PatientController extends HippoSubdomainController
{
	public function index(Request $request)
	{
		$query = Patient::on($request->header("Subdomain"));

		$sort = $request->get("sort");

		$page = $request->get("page") ?: 1;
		$limit = $request->get("limit") ?: 10;

		$query
			->leftJoin(
				"tblPatientOwners",
				"tblClients.id",
				"=",
				"tblPatientOwners.client_id",
			)
			->leftJoin(
				"tblPatientOwnerInformation",
				"tblPatientOwners.owner_id",
				"=",
				"tblPatientOwnerInformation.id",
			)
			->select(
				"tblClients.*",
				"tblPatientOwnerInformation.first_name as owner_first_name",
				"tblPatientOwnerInformation.last_name as owner_last_name",
				"tblPatientOwnerInformation.id as owner_id",
			)
			->addSelect([
				"photoName" => PatientImage::on(request()->header("Subdomain"))
					->select("name")
					->whereColumn("client_id", "tblClients.id")
					->limit(1),
			])
			->where("tblPatientOwners.primary", "=", true)
			->addSelect(
				DB::raw(
					"concat(tblPatientOwnerInformation.first_name, ' ', tblPatientOwnerInformation.last_name) as owner_full_name",
				),
			);

		if ($request->get("ownerName") !== "") {
			$ownerNameQueryString = implode(
				"%",
				explode(" ", $request->get("ownerName")),
			);

			if (strlen($ownerNameQueryString) > 0) {
				$query->having(
					"owner_full_name",
					"like",
					"%" . $ownerNameQueryString . "%",
				);
			}
		}

		if (
			$request->get("patientName") !== "" &&
			strlen($request->get("patientName")) > 0
		) {
			$query->where(
				"tblClients.first_name",
				"like",
				"%" . $request->get("patientName") . "%",
			);
		}

		$query = $this->addSortCriteria($query, $sort);

		$patientsPaginated = $query->paginate($limit, ["*"], "page", $page);

		$patientsWithImages = $patientsPaginated
			->getCollection()
			->map(function ($patient) {
				if ($patient->photoName) {
					$patient->photoLink = $this->generateS3ImageLink(
						$patient->photoName,
					);
				} else {
					$patient->photoLink = "/img/hippo-avatar.svg";
				}

				return $patient;
			});

		$patientsWithImagesPaginated = new LengthAwarePaginator(
			$patientsWithImages,
			$patientsPaginated->total(),
			$patientsPaginated->perPage(),
			$patientsPaginated->currentPage(),
		);

		return $patientsWithImagesPaginated;
	}

	protected function generateS3ImageLink($photoName)
	{
		return Storage::disk("s3-photos")->temporaryUrl(
			$photoName,
			now()->addMinutes(10),
		);
	}
}
