<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Degree
 *
 * @property int $id
 * @property string $degree
 * @property boolean $doctoral
 */
class Degree extends HippoModel
{
	use HasFactory;

	public $timestamps = false;

	protected $table = "tblDegrees";

	protected $fillable = ["degree", "doctoral"];
}
