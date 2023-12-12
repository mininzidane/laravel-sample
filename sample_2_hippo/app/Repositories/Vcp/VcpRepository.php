<?php

namespace App\Repositories\Vcp;

use App\Models\OrganizationSetting;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Log;

class VcpRepository extends Model
{
	/**
	 * @var array|string|null
	 */
	private $subdomain;

	private $password;
	private $username;
	private $authToken;
	private $refreshToken;
	private $refreshTokenTtl;
	private $isActive;
	private $contractArray;
	private $valid = true;
	/**
	 * @var Builder[]|Collection|null
	 */
	private $vcpContractId;
	/**
	 * @var array|mixed
	 */
	private $pdfString;

	//    public function __construct()
	//    {
	//
	//    }

	public function getContractHealth($subdomain, $id)
	{
		return $this->setSubdomain($subdomain)
			->getVcpParameters()
			->getUserContractId($id)
			->getNewAccessToken()
			->setContract()
			->getContractStatus();
	}

	public function getClientContractPdf($subdomain, $id)
	{
		return $this->setSubdomain($subdomain)
			->getVcpParameters()
			->getUserContractId($id)
			->getNewAccessToken()
			->setContract()
			->getContractPdf();
	}

	/**
	 * Retrieves the VCP contract Id for the Patient
	 *
	 * @param string $id
	 * @return VcpRepository
	 */
	private function getUserContractId($id): VcpRepository
	{
		if ($this->getIsActive() !== 0 && $this->getIsActive() !== null) {
			$this->setUserVcpContractId(
				Patient::on($this->getSubdomain())
					->where("id", $id)
					->value("vcp_contract_id"),
			);

			if (!isset($this->vcpContractId) || $this->vcpContractId == "") {
				Log::error("No VCP contract id found for " . $id);
				$this->valid = false;
			}
		} else {
			$this->valid = false;
		}

		return $this;
	}

	/**
	 * Gets tblOrganization parameters for VCP
	 *
	 * @return VcpRepository
	 */
	private function getVcpParameters(): VcpRepository
	{
		if ($this->valid) {
			$vcpParameters = OrganizationSetting::on($this->getSubdomain())
				->orWhere("setting_name", "vcp_password")
				->orWhere("setting_name", "vcp_username")
				->orWhere("setting_name", "vcp_auth_token")
				->orWhere("setting_name", "vcp_refresh_token")
				->orWhere("setting_name", "vcp_refresh_token_ttl")
				->orWhere("setting_name", "vcp_active")
				->get();

			$this->setPassword(
				$vcpParameters
					->where("setting_name", "=", "vcp_password")
					->first()->setting_value ?? null,
			);
			$this->setUsername(
				$vcpParameters
					->where("setting_name", "=", "vcp_username")
					->first()->setting_value ?? null,
			);
			$this->setAuthToken(
				$vcpParameters
					->where("setting_name", "=", "vcp_auth_token")
					->first()->setting_value ?? null,
			);
			$this->setRefreshToken(
				$vcpParameters
					->where("setting_name", "=", "vcp_refresh_token")
					->first()->setting_value ?? null,
			);
			$this->setRefreshTokenTtl(
				$vcpParameters
					->where("setting_name", "=", "vcp_refresh_token_ttl")
					->first()->setting_value ?? null,
			);
			$this->setIsActive(
				$vcpParameters
					->where("setting_name", "=", "vcp_active")
					->first()->setting_value ?? null,
			);
		}

		return $this;
	}

	/**
	 * Makes request to VCP for new Access Token
	 *
	 * @return VcpRepository
	 */
	private function getNewAccessToken(): VcpRepository
	{
		if ($this->valid) {
			try {
				$response = Http::asForm()->post(
					$this->getRefreshAuthTokenEndPoint(),
					[
						"grant_type" => "refresh_token",
						"refresh_token" => $this->refreshToken,
					],
				);

				$this->setAuthToken($response["access_token"]);
				$this->setRefreshToken($response["refresh_token"]);
				$this->setRefreshTokenTtl($response["expires_in"]);
			} catch (\Exception $e) {
				$response = Http::asForm()->post($this->getLoginUrl(), [
					"username" => $this->getUsername(),
					"password" => $this->getPassword(),
				]);
			}

			if ($response->failed()) {
				$this->sendFailedResponse($response);
			}

			if ($response->successful()) {
				$this->setAuthToken($response["access_token"]);
				$this->setRefreshToken($response["refresh_token"]);
				$this->setRefreshTokenTtl($response["expires_in"]);
			}
		}

		return $this;
	}

	/**
	 * Makes request to VCP to get the contract
	 * @return VcpRepository
	 */
	private function setContract(): VcpRepository
	{
		if ($this->valid) {
			$response = Http::withToken($this->getAuthToken())->get(
				$this->getContractUrlEndPoint(),
			);

			if ($response->failed()) {
				$this->sendFailedResponse($response);
			}

			$this->contractArray = $response->json();
		}

		return $this;
	}

	private function setContractPdf()
	{
		if ($this->valid) {
			$response = Http::withToken($this->getAuthToken())->get(
				$this->getContractPdfEndPoint(),
			);
			if ($response->failed()) {
				$this->sendFailedResponse($response);
			}
			$this->pdfString = $response->json();
		}
	}

	public function getContractPdf()
	{
		$this->setContractPdf();

		if (!$this->valid) {
			return "Error";
		}

		return response(
			base64_decode($this->pdfString["documentFileBase64"]),
		)->header("Content-Type", "application/pdf");
	}

	public function getContractStatus()
	{
		if (!$this->valid) {
			return "Error";
		}

		return $this->contractArray["accountHealth"];
	}

	private function getContractPeriodId()
	{
		return $this->contractArray["contractPeriod"]["id"];
	}

	/**
	 * @param \Illuminate\Http\Client\Response $response
	 */
	private function sendFailedResponse($response)
	{
		$this->valid = false;
		$errors = $response->toPsrResponse();
		$errorPhrase = $errors->getReasonPhrase();
		Log::error($errorPhrase . " There was an error logging into VCP");
		//return response()->json(['error' => $errors->getReasonPhrase(), 'data' => 'There was an error logging into VCP'], $errors->getStatusCode())->send();
	}

	/**
	 * Get the default options for an HTTP request.
	 * @return string[][]
	 */
	protected function getRequestOptions(): array
	{
		return [
			"headers" => [
				"Authorization" => "Bearer " . $this->getAuthToken(),
			],
		];
	}

	protected function getLoginUrl(): string
	{
		return $this->getBaseUrl() . "api/login";
	}

	protected function getContractUrlEndPoint(): string
	{
		return $this->getBaseUrl() .
			"api/contract/" .
			$this->getUserVcpContractId();
	}

	protected function getContractPdfEndPoint(): string
	{
		return $this->getBaseUrl() .
			"api/contractReport/" .
			$this->getContractPeriodId() .
			".json";
	}

	protected function getRefreshAuthTokenEndPoint(): string
	{
		return $this->getBaseUrl() . "oauth/access_token";
	}

	private function getBaseUrl()
	{
		return config("vcp.vcp.uri");
	}

	//Basic getter setter functions below
	private function setAuthToken($auth_token)
	{
		$this->authToken = $auth_token;
	}

	private function getAuthToken()
	{
		return $this->authToken;
	}

	private function setRefreshToken($refresh_token)
	{
		$this->refreshToken = $refresh_token;
	}

	private function setRefreshTokenTtl($refresh_token_ttl)
	{
		$this->refreshTokenTtl = $refresh_token_ttl;
	}

	private function setSubdomain($subdomain): VcpRepository
	{
		$this->subdomain = $subdomain;

		return $this;
	}

	private function getSubdomain()
	{
		return $this->subdomain;
	}

	private function setUserVcpContractId($id)
	{
		$this->vcpContractId = $id;
	}

	private function getUserVcpContractId()
	{
		return $this->vcpContractId;
	}

	public function getUsername()
	{
		return $this->username;
	}

	public function setUsername($username)
	{
		$this->username = decrypt($username);
	}

	public function getPassword()
	{
		return $this->password;
	}

	public function setPassword($password)
	{
		$this->password = decrypt($password);
	}

	public function getIsActive()
	{
		return $this->isActive;
	}

	public function setIsActive($isActive)
	{
		$this->isActive = $isActive;
	}
}
