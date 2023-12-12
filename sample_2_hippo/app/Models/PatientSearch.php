<?php

namespace App\Models;

class PatientSearch extends HippoModel
{
	protected $table = "patient_search";

	protected $appends = ["primary_image"];

	public function images()
	{
		return $this->hasMany(PatientImage::class, "client_id");
	}

	public function vaccines()
	{
		return $this->hasMany(Vaccination::class, "client_id");
	}

	public function invoiceItems()
	{
		return $this->hasManyThrough(
			InvoiceItem::class,
			Invoice::class,
			"patient_id",
			"invoice_id",
			"id",
			"id",
		);
	}

	public function getPrimaryImageAttribute(): string
	{
		return $this->images()
			->latest()
			->first()->presignedUrl ?? "img/hippo-avatar.svg";
	}
}
