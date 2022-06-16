<?php

namespace App\Http\Controllers\B2B;

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

class MapController extends Controller
{
    public function index(Request $request)
    {
    	// 取得會員ID
        $UID = WebLib::getCurrentUserID();
        // 取得會員資料並獲得UserType、DegreeId
        $user = DimUser::where('Id',$UID)->select('DegreeId','UserType')->first();
        // dd($UID,$UserType,$DegreeId);
        return view('B2B.Map.index');
    }
}