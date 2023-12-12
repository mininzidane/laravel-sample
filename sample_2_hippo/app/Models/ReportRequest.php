<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class ReportRequest extends HippoModel
{
	protected $table = "report_requests";

	protected $fillable = [
		"name",
		"user_id",
		"is_ready",
		"file_name",
		"format",
	];

	protected $appends = ["presignedUrl"];

	protected function user(): HasOne
	{
		return $this->hasOne(User::class);
	}

	public function getPresignedUrlAttribute()
	{
		return Storage::disk("s3-reports")->temporaryUrl(
			$this->file_name,
			now()->addMinutes(10),
		);
	}
}
