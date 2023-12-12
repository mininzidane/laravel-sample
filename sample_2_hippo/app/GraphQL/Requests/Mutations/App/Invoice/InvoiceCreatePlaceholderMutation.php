<?php

namespace App\GraphQL\Requests\Mutations\App\Invoice;

/**
 * Mutation to be called when User switching Patient
 * The only difference between this one and InvoiceCreateMutation should be the permission:
 * "Invoice: Create Placeholder" permission should be linked to each existed role to allow every User to switch Patient.
 */
class InvoiceCreatePlaceholderMutation extends InvoiceCreateMutation
{
	protected $permissionName = "Invoice: Create Placeholder";
}
