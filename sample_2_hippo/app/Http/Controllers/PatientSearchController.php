<?php

namespace App\Http\Controllers;

use App\Models\PatientSearch;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;

class PatientSearchController extends Controller
{
	/**
	 * @throws Exception
	 */
	public function index(Request $request): LengthAwarePaginator
	{
		$subdomain = $request->header("subdomain");

		if (empty($subdomain)) {
			throw new MissingMandatoryParametersException(
				"A subdomain must be specified in the request",
			);
		}

		$query = PatientSearch::on($subdomain)
			->select([
				"patient_search.id",
				"patient_search.name",
				"species",
				"gender",
				"primary_owner_first_name",
				"primary_owner_last_name",
				"date_of_death",
			])
			->distinct("patient_search.id");

		// The query parameter for deceased is interpreted as a string.
		if ($request->input("deceased") === "false") {
			$query->where(function ($q) {
				$q->whereNull("date_of_death")->orWhere(
					"date_of_death",
					"=",
					"0000-00-00",
				);
			});
		}

		if ($request->input("ownerOnly") === "true") {
			$query->where("owner_id", $request->input("ownerId"));
		}

		$smartSearch = $request->input("smartSearch");

		if ($smartSearch) {
			$smartSearchColumns = [
				"full_name",
				"id",
				"alias_id",
				"owner_full_name",
				"owner_id",
				"owner_email",
				"owner_phone",
				"owner_phone_2",
				"owner_phone_3",
				"license",
				"microchip",
			];

			$query->search($smartSearchColumns, $smartSearch);
		} else {
			if ($request->input("patientName")) {
				$query->whereLike("name", $request->input("patientName"));
			}

			if ($request->input("ownerFirstName")) {
				$query->whereLike(
					"owner_first_name",
					$request->input("ownerFirstName"),
				);
			}

			if ($request->input("ownerLastName")) {
				$query->whereLike(
					"owner_last_name",
					$request->input("ownerLastName"),
				);
			}

			if ($request->input("email")) {
				$query->whereLike("owner_email", $request->input("email"));
			}

			if ($request->input("phone")) {
				$query->whereLike(
					["owner_phone", "owner_phone_2", "owner_phone_3"],
					$request->input("phone"),
				);
			}

			if ($request->input("licenseId")) {
				$query->whereLike("license", $request->input("licenseId"));
			}

			if ($request->input("microchipId")) {
				$query->whereLike("microchip", $request->input("microchipId"));
			}

			if ($request->input("serialNumber")) {
				$query->whereLike(
					["invoiceItems.serial_number", "vaccines.serialnumber"],
					$request->input("serialNumber"),
				);
			}
		}

		$sort = $request->input("sort");

		if ($sort) {
			foreach ($this::getSortValues($sort) as $column => $direction) {
				$query->orderBy($column, $direction);
			}
		} else {
			$query
				->orderBy("name", "asc")
				->orderBy("primary_owner_last_name", "asc")
				->orderBy("primary_owner_first_name", "asc");
		}

		return $query->paginate(
			$request->input("limit") ?? 10,
			["*"],
			"currentPage",
			$request->input("currentPage") ?? 1,
		);
	}

	private static function getSortValues($sortString): array
	{
		return array_merge(
			[],
			...array_map(function ($column) {
				$components = explode(":", strtolower($column));

				return [
					$components[0] => in_array($components[1], ["asc", "desc"])
						? $components[1]
						: "asc",
				];
			}, explode(",", $sortString)),
		);
	}
}
