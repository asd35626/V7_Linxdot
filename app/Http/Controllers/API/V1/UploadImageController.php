<?php

namespace App\Http\Controllers\API\V1;;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Response;
use Uuid;
//use Storage;
class UploadImageController extends Controller
{
    //
    public function store(Request $request){
      //$s3 = Storage::disk('s3');
      $path = $request->input('path', 'files/upload/');
      // dd($path);
			if ($request->hasFile('file')) {
				$file = $request->file('file');

				if ($file->isValid()) {
          $fileExtension = strtolower($file->getClientOriginalExtension());

          if(($fileExtension == "jpg")
            or($fileExtension == "png")
            or($fileExtension == "gif")
          ){
            $fileName = str_random(15) .".". $file->getClientOriginalExtension();
            $photo = $path . $fileName;

            $getNewFileName = true;
  					while($getNewFileName){
  						//if($s3->exists($path .$fileName)){//檢查是否已有此檔案名稱
  						if(file_exists($path .$fileName)){	
                $fileName = str_random(15) .".". $file->getClientOriginalExtension();
  							$photo = $path . $fileName;
  						}else{
  							$getNewFileName = false;
  						}
  					}

            //$t = $s3->put($photo, file_get_contents($file), 'public');
  					$file->move(storage_path($path), $photo);
            $responseBody['location'] = $photo;
            return Response::json($responseBody, 200);
          }else{
            $responseBody['message'] = 'File is invalid';
              		return Response::json($responseBody, 422);
          }
				}else{
					$responseBody['message'] = 'File is invalid';
            		return Response::json($responseBody, 422);
				}
			}else{
				$responseBody['message'] = 'Please Provide Data';
            	return Response::json($responseBody, 422);
			}
    }
}
