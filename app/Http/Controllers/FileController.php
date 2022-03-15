<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\V7Idea\WebLib;
use App\SystemErrorLog;
use Carbon\Carbon;
use Exception;

class FileController extends Controller
{
    //取得圖片的路徑
    public function getFile($folder,$filename)
    {
    	$filename = "files/".$folder."/" . $filename;
        return response()->download(storage_path($filename), null, [], null);
    }
}
