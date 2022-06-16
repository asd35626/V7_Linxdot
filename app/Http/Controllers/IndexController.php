<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\DailyReport;
use App\Comments;
use App\BulletinBoard;
use App\DimActive;
use App\ApplicationFrom;
use App\UserProcessTicket;
use App\Model\DimUser;
use App\SystemErrorLog;
use Hash;
use Uuid;
use Response;
use Carbon\Carbon;
use App\V7Idea\WebLib;

class IndexController extends Controller
{
    public function index(Request $request)
    {
    	// 取得會員ID
        $UID = WebLib::getCurrentUserID();
        // 取得會員資料並獲得UserType、DegreeId
        $user = DimUser::where('Id',$UID)->select('DegreeId','UserType')->first();
        $UserType = $user->UserType;
        $DegreeId = $user->DegreeId;
        // dd($UID,$UserType,$DegreeId);

        if($UserType == 20 && $DegreeId == 50){
        	return redirect()->route('Dashboard.index');
        }else{
        	return view('/Default');
        }
    }
}