<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use Uuid;
use Carbon\Carbon;
use App\V7Idea\WebLib;
use App\V7Idea\GenerateData;

class testController extends Controller
{
    // 設定blade目錄的位置
    public static $viewPath = "test";
    
    // 設定route目錄的位置
    public static $routePath = "test";

    // 這個資料表的主要鍵值
    public static $primaryKey = "id";

    // 設定功能主選單名稱名稱
    public static $TOPname = "test";

    // 設定功能名稱
    public static $functionname = "test";

    // 設定功能UEL
    public static $functionURL = "/test";

    public function index()
    {
        return view(self::$viewPath.'.index')
            ->with('routePath', self::$routePath)
            ->with('viewPath', self::$viewPath)
            ->with('functionname', self::$functionname)
            ->with('functionURL', self::$functionURL)
            ->with('TOPname', self::$TOPname);
    }


    public function create()
    {
        return view(self::$viewPath.'.create')
            ->with('routePath', self::$routePath)
            ->with('Action', "NEW")
            ->with('viewPath', self::$viewPath)
            ->with('functionname', self::$functionname)
            ->with('functionURL', self::$functionURL)
            ->with('TOPname', self::$TOPname);
    }

    public function store(Request $request)
    {
        return redirect()->route(self::$routePath.'.create')
                        ->with('success','成功新增一筆資料！');
    }
}