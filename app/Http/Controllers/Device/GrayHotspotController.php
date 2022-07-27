<?php
namespace App\Http\Controllers\Device;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use App\Model\DimGrayHotspot;
use App\Model\DimUser;
use Uuid;
use Response;
use Carbon\Carbon;
use App\V7Idea\WebLib;
use App\V7Idea\GenerateData;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GrayHotspotController extends Controller
{
    /// <summary>
    /// 檢查權限
    /// </summary>
    function __construct(){
        WebLib::checkUserPermission('GrayHotspot');
    }
    // 設定blade目錄的位置
    public static $viewPath = "Device.GrayHotspot";
    
    // 設定route目錄的位置
    public static $routePath = "GrayHotspot";

    // 這個資料表的主要鍵值
    public static $primaryKey = "id";

    // 設定功能主選單名稱名稱
    public static $TOPname = "Device";

    // 設定功能名稱
    public static $functionname = "Gray Hotspot";

    // 設定功能名稱
    public static $functionURL = "/Device/GrayHotspot";

    // 定義搜尋的欄位設定;
    public function defineSearchFields() {
        $fields = [ 
            'S/N' =>  [
                'name' => 'S/N',
                'id' => 'S/N',
                'label' => 'S/N',
                'type' => 'text',
                'value' => '',
                'class' => 'md-input label-fixed',
            ],
            'Mac' =>  [
                'name' => 'Mac',
                'id' => 'Mac',
                'label' => 'Mac',
                'type' => 'text',
                'value' => '',
                'class' => 'md-input label-fixed',
            ],
            'AnimalName' =>  [
                'name' => 'AnimalName',
                'id' => 'AnimalName',
                'label' => 'Animal Name',
                'type' => 'text',
                'value' => '',
                'class' => 'md-input label-fixed',
            ],
        ];
        return $fields;
    }

    public function defineFormFields() {
        $fields = [
            'DeviceSN' => [
                'name' => 'DeviceSN',
                'id' => 'DeviceSN',
                'label' => 's/n',
                'type' => 'text',
                'validation' => 'required',
                'value' => '',
                'class' => 'md-input label-fixed',
            ],
            'MacAddress' => [
                'name' => 'MacAddress',
                'id' => 'MacAddress',
                'label' => 'lan mac',
                'type' => 'text',
                'validation' => 'required',
                'value' => '',
                'class' => 'md-input label-fixed',
            ],
            'PalletId' => [
                'name' => 'PalletId',
                'id' => 'PalletId',
                'label' => 'PalletId',
                'type' => 'text',
                'validation' => '',
                'value' => '',
                'class' => 'md-input label-fixed',
            ],
            'CartonId' => [
                'name' => 'CartonId',
                'id' => 'CartonId',
                'label' => 'CartonId',
                'type' => 'text',
                'validation' => '',
                'value' => '',
                'class' => 'md-input label-fixed',
            ],
            'AnimalName' => [
                'name' => 'AnimalName',
                'id' => 'AnimalName',
                'label' => 'animal name',
                'type' => 'text',
                'validation' => '',
                'value' => '',
                'class' => 'md-input label-fixed',
            ],
            'IsVerify' => [
                'name' => 'IsVerify',
                'id' => 'IsVerify',
                'label' => 'verify status',
                'type' => 'radio',
                'selectLists' => [
                    '1' => 'Yes',
                    '0' => 'No'
                ],
                'value' => '1',
                'validation' => 'required',
                'class' => 'md-input label-fixed',
            ],
            'IfValid' => [
                'name' => 'IfValid',
                'id' => 'IfValid',
                'label' => 'Status',
                'type' => 'radio',
                'selectLists' => [
                    '1' => 'Active',
                    '0' => 'Inactive'
                ],
                'value' => '1',
                'validation' => 'required',
                'class' => 'md-input label-fixed',
            ],
            'CreateBy' => [
                'name' => 'CreateBy',
                'id' => 'CreateBy',
                'label' => 'Created By',
                'type' => 'text',
                'value' => 'CreateBy',
                'validation' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ],
            'CreateDate' => [
                'name' => 'CreateDate',
                'id' => 'CreateDate',
                'label' => 'Created Date',
                'type' => 'text',
                'validation' => '',
                'value' => 'Created Date',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ],
        ];
        return $fields;
    }

    public function defineEditFormFields($data) {
        $fields = [
            'AnimalName' => [
                'name' => 'AnimalName',
                'id' => 'AnimalName',
                'label' => 'Animal Name',
                'type' => 'text',
                'validation' => '',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ],
            'OnBoardingKey' => [
                'name' => 'OnBoardingKey',
                'id' => 'OnBoardingKey',
                'label' => 'OnBoardingKey',
                'type' => 'text',
                'validation' => '',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ],
            'DeviceSN' => [
                'name' => 'DeviceSN',
                'id' => 'DeviceSN',
                'label' => 'S/N',
                'type' => 'text',
                'validation' => '',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ],
            'MacAddress' => [
                'name' => 'MacAddress',
                'id' => 'MacAddress',
                'label' => 'MacAddress',
                'type' => 'text',
                'validation' => '',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ],
            'WifiMac' => [
                'name' => 'WifiMac',
                'id' => 'WifiMac',
                'label' => 'WifiMac',
                'type' => 'text',
                'validation' => '',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ],
            'MgrVersion' => [
                'name' => 'MgrVersion',
                'id' => 'MgrVersion',
                'label' => 'MgrVersion',
                'type' => 'text',
                'validation' => '',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ],
            'MinerVersion' => [
                'name' => 'MinerVersion',
                'id' => 'MinerVersion',
                'label' => 'MinerVersion',
                'type' => 'text',
                'validation' => '',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ],
            'Firmware' => [
                'name' => 'Firmware',
                'id' => 'Firmware',
                'label' => 'Firmware',
                'type' => 'text',
                'validation' => '',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ],
            'Region' => [
                'name' => 'Region',
                'id' => 'Region',
                'label' => 'Region',
                'type' => 'text',
                'validation' => '',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ],
            'GrayMemo' => [
                'name' => 'GrayMemo',
                'id' => 'GrayMemo',
                'label' => 'GrayMemo',
                'type' => 'text',
                'validation' => '',
                'value' => '',
                'class' => 'md-input label-fixed',
            ],
            'IsFixed' => [
                'name' => 'IsFixed',
                'id' => 'IsFixed',
                'label' => 'IsFixed',
                'type' => 'radio',
                'selectLists' => [
                    '1' => 'Yes',
                    '0' => 'No'
                ],
                'value' => '1',
                'validation' => 'required',
                'class' => 'md-input label-fixed',
            ],
            'HotspoType' => [
                'name' => 'HotspoType',
                'id' => 'HotspoType',
                'label' => 'HotspoType',
                'type' => 'radio',
                'selectLists' => [
                    '1' => 'Yes',
                    '0' => 'No'
                ],
                'value' => '1',
                'validation' => 'required',
                'class' => 'md-input label-fixed',
            ],
            'CreateBy' => [
                'name' => 'CreateBy',
                'id' => 'CreateBy',
                'label' => 'Created By',
                'type' => 'text',
                'value' => 'CreateBy',
                'validation' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ],
            'CreateDate' => [
                'name' => 'CreateDate',
                'id' => 'CreateDate',
                'label' => 'Created Date',
                'type' => 'text',
                'validation' => '',
                'value' => 'CreateDate',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ],
        ];
        return $fields;
    }

    public function index(Request $request)
    {
        $pageNumEachPage = 100;                             // 每頁的基本資料量
        $pageNo = (int) $request->input('Page', '1');       // 目前的頁碼
        $IsNewSearch = $request->input('IfNewSearch', '');  // 是否為新開始搜尋
        $IfSearch = $request->input('IfSearch', '');        // 是否為搜尋
        $orderBy = $request->input('orderBy', '');          // 排序欄位
        $isAsc = $request->input('isAsc', '');              // 是否順序排序
        $excel = $request->input('excel', 0);               // 是否匯出excel

        // 產生搜尋的欄位;
        $searchFields = WebLib::generateInputs(self::defineSearchFields(), true)["data"];
        //dd($searchFields);

        // 當按下搜尋的時候，會傳回IfNewSearch = 1; 如果不是，表示空值或是其他數值;
        // 當是其他數值的時候，會依照原來的頁碼去產生回應的頁面;
        if($IsNewSearch != '1') {
            Paginator::currentPageResolver(function () use ($pageNo) {
                return $pageNo;
            });
        }else{
            $pageNo = 1;
        }

        $data = DimGrayHotspot::where('IfDelete','0');

        if ($IfSearch == '1') {
            // 表示會需要參考搜尋的變數
            $searchArray = array(
                'S/N' => $searchFields['S/N']['value'],
                'Mac' => strtolower(str_replace("-",":",$searchFields['Mac']['value'])),
                'AnimalName' => $searchFields['AnimalName']['value'],
            );
            $data= $data->where(function($query) use ($searchArray) {
                if($searchArray['S/N'] != '') {
                    $query->where('DeviceSN', 'like', '%'.$searchArray['S/N'].'%' );
                }
                if($searchArray['Mac'] != '') {
                    $query->where('MacAddress', 'like', '%'.$searchArray['Mac'].'%' );
                }
                if($searchArray['AnimalName'] != '') {
                    $query->where('AnimalName', 'like', '%'.$searchArray['AnimalName'].'%' );
                }
            });
        }

        //排序
        if(isset($orderBy) && $orderBy != ''){
            if($isAsc == '1'){
                $data = $data->orderBy($orderBy, 'ASC');
            }else{
                $data = $data->orderBy($orderBy, 'DESC');
            }
        }else{
            $data = $data->orderBy('CreateDate');
        }
        $data = $data->paginate($pageNumEachPage);

        return view(self::$viewPath.'.index', compact('data'))
                    ->with('i', ($pageNo - 1) * $pageNumEachPage)
                    ->with('IfSearch', $IfSearch)
                    ->with('pageNo', $pageNo)
                    ->with('searchFields',  $searchFields)
                    ->with('routePath', self::$routePath)
                    ->with('viewPath', self::$viewPath)
                    ->with('primaryKey', self::$primaryKey)
                    ->with('orderBy', $orderBy)
                    ->with('isAsc', $isAsc)
                    ->with('pageNumEachPage', $pageNumEachPage)
                    ->with('functionname', self::$functionname)
                    ->with('functionURL', self::$functionURL)
                    ->with('TOPname', self::$TOPname);
    }

    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function create()
    {
        // 取得輸入欄位的定義
        $formFieldDef = self::defineFormFields();

        // 產生需要設定的欄位
        $formFields = WebLib::generateInputs($formFieldDef, false)["data"];

        return view(self::$viewPath.'.create')
                    ->with('formFields', $formFields)
                    ->with('routePath', self::$routePath)
                    ->with('Action', "NEW")
                    ->with('viewPath', self::$viewPath)
                    ->with('functionname', self::$functionname)
                    ->with('functionURL', self::$functionURL)
                    ->with('TOPname', self::$TOPname);
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request)
    {
        return redirect()->route(self::$routePath.'.index')
                        ->with('success','');
    }
    /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function show($id)
    {
        return redirect()->route(self::$routePath.'.index')
                        ->with('success','');
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $id = (string) $id;
        $data= DimGrayHotspot::where('id', $id)
                        ->where('IfDelete', 0)
                        ->get();

        // 取得輸入欄位的定義
        $formFieldDef = self::defineEditFormFields($data->first());
        // dd($formFieldDef);

        // 把資料放進對應欄位
        $requestResult =  WebLib::generateInputsWhthData($formFieldDef, $data);
        // 修正資料建立者
        $requestResult['CreateBy']['value'] = GenerateData::getCreater($data->first()->CreateBy);
        // 修正資料建立者
        $CreateDate = 'CreateDate';
        if($data->first()->CreateDate != ''){
            $CreateDate = $data->first()->CreateDate;
        }
        $requestResult['CreateDate']['value'] = $CreateDate;
        // 產生需要設定的欄位  
        $requestResult = WebLib::generateInputs($requestResult, false);
        // 把產生好的欄位取出來
        $formFields = $requestResult["data"];

        return view(self::$viewPath.'.edit', compact('data'))
                    ->with('formFields', $formFields)
                    ->with('routePath', self::$routePath)
                    ->with('targetId', $id)
                    ->with('Action', "EDIT")
                    ->with('primaryKey', self::$primaryKey)
                    ->with('viewPath', self::$viewPath)
                    ->with('functionname', self::$functionname)
                    ->with('functionURL', self::$functionURL)
                    ->with('TOPname', self::$TOPname);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $id = (string) $id;
        $data= DimGrayHotspot::where('id', $id)->first();
        $formFieldDef = $this->defineEditFormFields($data);
        $requestResult = WebLib::generateInputs($formFieldDef, true);
        $formFields = $requestResult["data"];
        $errmsg = "";
        $MacAddress = $formFields['MacAddress']['value'];
        $newMacAddress = '';

        if($requestResult['isError'] == false) {
            $updateValue = [
                'IsFixed' => $formFields['IsFixed']['value'],
                'HotspoType' => $formFields['HotspoType']['value'],
                'GrayMemo' => $formFields['GrayMemo']['value']
            ];

            DimGrayHotspot::on('mysql2')->where('id', $id)->update($updateValue);
            return redirect()->route(self::$routePath.'.index')
                            ->with('success','更新完成');
        }else{
            //重新產生原本的資料            
            $IsFixed= ($formFields['IsFixed']['value'] == '') ? $data->IsFixed : $formFields['IsFixed']['value'];
            $formFields['IsFixed']['completeField'] = GenerateData::getRadioHtml('IsFixed','IsFixed','No','Yes',$IsFixed);

            $CreateBy = GenerateData::getCreater($data->first()->CreateBy);
            $formFields['CreateBy']['value'] = $CreateBy;
            $formFields['CreateBy']['completeField'] = GenerateData::generateData('CreateBy','CreateBy', $CreateBy, '', 'text');

            $formFields['CreateDate']['value'] = ($formFields['CreateDate']['value'] == '') ? $data->CreateDate : $formFields['CreateDate']['value'];
            $formFields['CreateDate']['completeField'] = GenerateData::generateData('CreateDate','CreateDate', $formFields['CreateDate']['value'], '', 'text');

            return view(self::$viewPath.'.edit', compact('data'))
                    ->with('formFields', $formFields)
                    ->with('routePath', self::$routePath)
                    ->with('targetId', $id)
                    ->with('primaryKey', self::$primaryKey)
                    ->with('Action', "EDIT")
                    ->with('viewPath', self::$viewPath)
                    ->with('errmsg', $errmsg)
                    ->with('functionname', self::$functionname)
                    ->with('functionURL', self::$functionURL)
                    ->with('TOPname', self::$TOPname);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $authToken = \Cookie::get('authToken');
        return redirect()->route(self::$routePath.'.index')
                            ->with('success','資料已經刪除');
    }
}