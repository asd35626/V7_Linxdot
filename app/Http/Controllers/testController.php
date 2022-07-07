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
    public function test(Request $request){
        $responseBody = array(
          'status' => 0,
          'data' => []
        );

        $mac = $request->mac;
        //設定API網址，要傳給API的json
        $api="http://192.168.150.163:49880/registDewi";
        $ch = curl_init($api);
        $data_string = '{"mac":"'.$mac.'"}';

        //設定使用POST方式傳輸
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // 取出回傳值
        $result = curl_exec($ch);
        //關閉url
        curl_close($ch);
        //將回傳值轉為array
        $responseBody['data'] = json_decode($result,true);
        

        return Response($responseBody, 200);
    }
}