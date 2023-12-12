<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;

class EmptyInvoicesController extends Controller
{
	public function purge(Request $request, $subdomain): JsonResponse
	{
		if ($subdomain) {
			try {
				$this->createSubdomainConnection($subdomain);
				// Get all Invoices with NO associated Invoice Items, this includes Invoice Items that were trashed.
				// Even if the Invoice has no active items, and all associated items are trashed it should not be included. Some Items/Item Kits may be associated with procedures.
				$query = Invoice::on($subdomain)
					->has("invoiceItemsWithTrashed", "=", 0)
					->has("invoicePayments", "=", 0)
					->withTrashed();
				$count = $query->count();

				if ($count > 0) {
					$query->forceDelete();
				}

				return response()->json([
					"purged" => $count,
					"message" => "Purged {$count} invoice(s) with no associated invoice items on subdomain '{$subdomain}'.",
				]);
			} catch (Exception $e) {
				return response()->json($e->getMessage(), 500);
			}
		}
		return response()->json(
			"No 'Subdomain' header was included with the request.",
			400,
		);
	}
}
