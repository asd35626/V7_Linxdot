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

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'http://192.168.150.163:49880/registDewi',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => 'mac='.$mac,
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded'
          ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        

        //設定API網址，要傳給API的json
        $api="http://192.168.150.163:49880/registDewi";
        $ch = curl_init($api);

        // $data_string = '{"mac":"0c:86:29:e0:10:9c"}';
        $data_string = ['mac' => "0c:86:29:e0:10:9c"];

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