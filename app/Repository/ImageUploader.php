<?php
namespace App\Repository;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * 
 */
class ImageUploader {
	public function up(UploadedFile $uploadedFile, $folder = null, $disk = 'public', $filename = null){

		$name = !is_null($filename) ? $filename : Str::random(25);
        $file = $uploadedFile->storeAs($folder, $name.'.'.$uploadedFile->getClientOriginalExtension(), $disk);
        return $file;
	}

	public function rem($folder = null, $filename = null){
		if(!is_null($filename)){
			$image_path = public_path().$folder.$filename;
			return unlink($image_path);
		}
	}
}