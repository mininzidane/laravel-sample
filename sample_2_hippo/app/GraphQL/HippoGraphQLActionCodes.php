<?php

declare(strict_types=1);

namespace App\GraphQL;

class HippoGraphQLActionCodes
{
	const CREDIT_CARD_AMOUNT_DUE_EXCEEDED = 100;

	// Clearent
	const CLEARENT_TERMINAL_UPDATE = 5010;

	// Credit
	const CREDIT_CREATE = 11000;
	const CREDIT_DELETE = 11001;
	const CREDIT_UPDATE = 11002;

	// Invoice
	const INVOICE_COMPLETE = 12000;
	const INVOICE_CREATE = 12001;
	const INVOICE_REOPEN = 12002;
	const INVOICE_SAVE_DETAILS = 12003;
	const INVOICE_SET_ACTIVE = 12004;
	const INVOICE_VOID = 12005;

	// Invoice Item
	const INVOICE_ITEM_BULK_ADD = 13000;
	const INVOICE_ITEM_CREATE = 13001;
	const INVOICE_ITEM_DELETE = 13002;
	const INVOICE_ITEM_UPDATE = 13003;

	// Invoice Payment
	const INVOICE_PAYMENT_CANCEL_CLEARENT = 14000;
	const INVOICE_PAYMENT_COMPLETE_CLEARENT = 14001;
	const INVOICE_PAYMENT_CREATE_ACCOUNT_CREDIT = 14002;
	const INVOICE_PAYMENT_CREATE_GIFT_CARD = 14003;
	const INVOICE_PAYMENT_CREATE_STANDARD = 14004;
	const INVOICE_PAYMENT_DELETE_ACCOUNT_CREDIT = 14005;
	const INVOICE_PAYMENT_DELETE_STANDARD = 14006;
	const INVOICE_PAYMENT_DISPENSE_CHANGE = 14007;
	const INVOICE_PAYMENT_INITIALIZE_CLEARENT = 14008;
	const INVOICE_PAYMENT_ISSUE_ACCOUNT_CREDIT_FOR_OVERPAYMENT = 14009;

	// Item
	const ITEM_CREATE = 15000;
	const ITEM_DELETE = 15001;
	const ITEM_UPDATE = 15002;

	// Item
	const ITEM_CATEGORY_CREATE = 16000;
	const ITEM_CATEGORY_DELETE = 16001;
	const ITEM_CATEGORY_UPDATE = 16002;

	// Patient Alert
	const PATIENT_ALERT_CREATE = 17000;
	const PATIENT_ALERT_DELETE = 17001;

	// Patient Allergy
	const PATIENT_ALLERGY_DELETE = 18000;
	const PATIENT_ALLERGY_NOTE_UPDATE = 18001;
	const PATIENT_ALLERGY_UPDATE = 18002;
	const PATIENT_DRUG_ALLERGY_DELETE = 18003;
	const PATIENT_DRUG_ALLERGY_UPDATE = 18004;
	const PATIENT_ALLERGY_CREATE = 18005;
	const PATIENT_DRUG_ALLERGY_CREATE = 18006;

	// Receiving
	const RECEIVING_COMPLETE = 19000;
	const RECEIVING_CREATE = 19001;
	const RECEIVING_SAVE_DETAILS = 19002;
	const RECEIVING_SET_ACTIVE = 19003;
	const RECEIVING_VOID = 19004;

	// Receiving Item
	const RECEIVING_ITEM_CREATE = 20000;
	const RECEIVING_ITEM_DELETE = 20001;
	const RECEIVING_ITEM_UPDATE = 20002;

	// Supplier
	const SUPPLIER_CREATE = 21000;
	const SUPPLIER_DELETE = 21001;
	const SUPPLIER_UPDATE = 21002;

	// Tax
	const TAX_CREATE = 22000;
	const TAX_DELETE = 22001;
	const TAX_UPDATE = 22002;

	// Patient Vaccination
	const PATIENT_VACCINATION_CREATE = 23000;
	const PATIENT_VACCINATION_DELETE = 23001;
	const PATIENT_VACCINATION_UPDATE = 23002;

	// Species
	const SPECIES_CREATE = 28000;
	const SPECIES_UPDATE = 28001;
	const SPECIES_DELETE = 28002;

	// Breeds
	const BREED_CREATE = 29000;
	const BREED_UPDATE = 29001;
	const BREED_DELETE = 29002;

	// Colors
	const COLOR_CREATE = 30000;
	const COLOR_UPDATE = 30001;
	const COLOR_DELETE = 30002;

	// Markings
	const MARKINGS_CREATE = 31000;
	const MARKINGS_UPDATE = 31001;
	const MARKINGS_DELETE = 31002;

	// Gender
	const GENDER_CREATE = 32000;
	const GENDER_UPDATE = 32001;
	const GENDER_DELETE = 32002;

	// Mucous Membrane Status
	const MUCOUSMEMBRANESTATUS_CREATE = 33000;
	const MUCOUSMEMBRANESTATUS_UPDATE = 33001;
	const MUCOUSMEMBRANESTATUS_DELETE = 33002;

	// Hydration Status
	const HYDRATIONSTATUS_CREATE = 34000;
	const HYDRATIONSTATUS_UPDATE = 34001;
	const HYDRATIONSTATUS_DELETE = 34002;
}