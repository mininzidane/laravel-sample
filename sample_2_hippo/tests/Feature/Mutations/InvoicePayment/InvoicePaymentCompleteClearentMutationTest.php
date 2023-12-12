<?php

namespace Tests\Feature\Mutations\InvoicePayment;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Location;
use App\Models\Payment;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class InvoicePaymentCompleteClearentMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	public function test_clearent_payment_completion_fails_without_invoice_payment()
	{
		$query = 'mutation invoicePaymentCompleteClearent(
			$input: invoicePaymentCompleteClearentInput!) {
				invoicePaymentCompleteClearent(input: $input) {data {id}}}';
		$input = [
			"input" => [],
		];

		$response = $this->postGraphqlJsonWithVariables($query, $input);

		$this->assertContains(
			"Please provide at least one invoice payment to update",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_clearent_payment_completion_fails_without_amount_tendered()
	{
		$query = 'mutation invoicePaymentCompleteClearent(
			$input: invoicePaymentCompleteClearentInput!) {
				invoicePaymentCompleteClearent(input: $input) {data {id}}}';
		$input = [
			"input" => [],
		];

		$response = $this->postGraphqlJsonWithVariables($query, $input);

		$this->assertContains(
			"The input.amount tendered field is required.",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_clearent_payment_completion_fails_without_request_id()
	{
		$query = 'mutation invoicePaymentCompleteClearent(
			$input: invoicePaymentCompleteClearentInput!) {
				invoicePaymentCompleteClearent(input: $input) {data {id}}}';
		$input = [
			"input" => [],
		];

		$response = $this->postGraphqlJsonWithVariables($query, $input);

		$this->assertContains(
			"The input.request id field is required.",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_clearent_payment_completion_fails_without_request_type()
	{
		$query = 'mutation invoicePaymentCompleteClearent(
			$input: invoicePaymentCompleteClearentInput!) {
				invoicePaymentCompleteClearent(input: $input) {data {id}}}';
		$input = [
			"input" => [],
		];

		$response = $this->postGraphqlJsonWithVariables($query, $input);

		$this->assertContains(
			"The input.request type field is required.",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_clearent_payment_completion_fails_without_response_status()
	{
		$query = 'mutation invoicePaymentCompleteClearent(
			$input: invoicePaymentCompleteClearentInput!) {
				invoicePaymentCompleteClearent(input: $input) {data {id}}}';
		$input = [
			"input" => [],
		];

		$response = $this->postGraphqlJsonWithVariables($query, $input);

		$this->assertContains(
			"The input.response status field is required.",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_clearent_payment_completion_fails_without_payment_method()
	{
		$query = 'mutation invoicePaymentCompleteClearent(
			$input: invoicePaymentCompleteClearentInput!) {
				invoicePaymentCompleteClearent(input: $input) {data {id}}}';
		$input = [
			"input" => [],
		];

		$response = $this->postGraphqlJsonWithVariables($query, $input);

		$this->assertContains(
			"The input.payment method field is required.",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_clearent_payment_completion_fails_without_payment_platform()
	{
		$query = 'mutation invoicePaymentCompleteClearent(
			$input: invoicePaymentCompleteClearentInput!) {
				invoicePaymentCompleteClearent(input: $input) {data {id}}}';
		$input = [
			"input" => [],
		];

		$response = $this->postGraphqlJsonWithVariables($query, $input);

		$this->assertContains(
			"The input.payment platform field is required.",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_clearent_payment_completion_fails_without_platform_mode()
	{
		$query = 'mutation invoicePaymentCompleteClearent(
			$input: invoicePaymentCompleteClearentInput!) {
				invoicePaymentCompleteClearent(input: $input) {data {id}}}';
		$input = [
			"input" => [],
		];

		$response = $this->postGraphqlJsonWithVariables($query, $input);

		$this->assertContains(
			"The input.platform mode field is required.",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_clearent_payment_completion_fails_without_owner()
	{
		$query = 'mutation invoicePaymentCompleteClearent(
			$input: invoicePaymentCompleteClearentInput!) {
				invoicePaymentCompleteClearent(input: $input) {data {id}}}';
		$input = [
			"input" => [],
		];

		$response = $this->postGraphqlJsonWithVariables($query, $input);

		$this->assertContains(
			"The input.owner field is required.",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_clearent_payment_completion_fails_without_terminal()
	{
		$query = 'mutation invoicePaymentCompleteClearent(
			$input: invoicePaymentCompleteClearentInput!) {
				invoicePaymentCompleteClearent(input: $input) {data {id}}}';
		$input = [
			"input" => [],
		];

		$response = $this->postGraphqlJsonWithVariables($query, $input);

		$this->assertContains(
			"The input.clearent terminal field is required.",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_clearent_payment_completion_fails_without_terminal_id()
	{
		$query = 'mutation invoicePaymentCompleteClearent(
			$input: invoicePaymentCompleteClearentInput!) {
				invoicePaymentCompleteClearent(input: $input) {data {id}}}';
		$input = [
			"input" => [],
		];

		$response = $this->postGraphqlJsonWithVariables($query, $input);

		$this->assertContains(
			"The input.terminal id field is required.",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_clearent_payment_completion_fails_without_response()
	{
		$query = 'mutation invoicePaymentCompleteClearent(
			$input: invoicePaymentCompleteClearentInput!) {
				invoicePaymentCompleteClearent(input: $input) {data {id}}}';
		$input = [
			"input" => [],
		];

		$response = $this->postGraphqlJsonWithVariables($query, $input);

		$this->assertContains(
			"The input.response field is required.",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_clearent_payment_completion_fails_without_location()
	{
		$query = 'mutation invoicePaymentCompleteClearent(
			$input: invoicePaymentCompleteClearentInput!) {
				invoicePaymentCompleteClearent(input: $input) {data {id}}}';
		$input = [
			"input" => [],
		];

		$response = $this->postGraphqlJsonWithVariables($query, $input);

		$this->assertContains(
			"The input.location id field is required.",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}
}
