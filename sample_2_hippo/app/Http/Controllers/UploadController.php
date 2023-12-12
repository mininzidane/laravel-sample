<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
	public function uploadLogoUrl(Request $request)
	{
		return Storage::disk("s3-logos")->awsTemporaryUploadUrl(
			$request->input("filename"),
			now()->addMinutes(15),
			["ACL" => "public-read"],
		);
	}

	public function deleteLogo(Request $request)
	{
		return Storage::disk("s3-logos")->delete($request->input("filename"));
	}
}
