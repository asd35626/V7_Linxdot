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
        $UID = WebLib::getCurrentUserID();
        // dd($UID);
        return view('/Default');
    }
}