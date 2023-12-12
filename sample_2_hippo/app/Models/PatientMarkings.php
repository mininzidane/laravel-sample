<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $client_id
 * @property string $markings
 *
 * @property-read Patient $patient
 *
 * @mixin \Eloquent
 */
class PatientMarkings extends HippoModel
{
	use SoftDeletes;
	use HasName;
	use HasFactory;

	public static $graphQLType = null;

	protected $table = "tblPatientAnimalMarkings";

	protected $fillable = ["client_id", "markings"];

	public function __construct(array $attributes = [])
	{
		$this->timestamps = false;

		parent::__construct($attributes);
	}

	public function patient(): BelongsTo
	{
		return $this->belongsTo(Patient::class, "client_id");
	}
}
