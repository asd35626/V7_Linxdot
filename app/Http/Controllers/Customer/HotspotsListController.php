<?php
namespace App\Http\Controllers\Customer;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use App\Model\DimHotspot;
use App\Model\DimUser;
use Uuid;
use Carbon\Carbon;
use App\V7Idea\WebLib;
use App\V7Idea\GenerateData;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class HotspotsListController extends Controller
{
    /// <summary>
    /// 檢查權限
    /// </summary>
    function __construct(){
        WebLib::checkUserPermission('B2B');
    }
    // 設定blade目錄的位置
    public static $viewPath = "Customer.HotspotsList";
    
    // 設定route目錄的位置
    public static $routePath = "Customer.B2B.HotspotsList";

    // 這個資料表的主要鍵值
    public static $primaryKey = "id";

    // 設定功能主選單名稱名稱
    public static $TOPname = "Customer";

    // 設定功能名稱
    public static $functionname = "Hotspots";

    // 設定功能名稱
    public static $functionURL = "/Customer/B2B";

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
            'IsVerify' => [
                'name' => 'IsVerify',
                'id' => 'IsVerify',
                'label' => 'IsVerify',
                'type' => 'select',
                'selectLists' => [
                    '' => 'Choose',
                    '0' => 'No',
                    '1' => 'Yes'
                ],
                'value' => '1',
                'class' => 'md-input label-fixed',
            ],
            'IfRegister' => [
                'name' => 'IfRegister',
                'id' => 'IfRegister',
                'label' => 'IfRegister',
                'type' => 'select',
                'selectLists' => [
                    '' => 'Choose',
                    '0' => 'No',
                    '1' => 'Yes'
                ],
                'value' => '1',
                'class' => 'md-input label-fixed',
            ],
            'IssueDateFrom' => [
                'name' => 'IssueDateFrom',
                'id' => 'IssueDateFrom',
                'label' => 'Issue Date From',
                'type' => 'date',
                'value' => '',
                'extras' => [
                    'placeholder' => '',
                    'data-parsley-trigger' => 'change',
                    'autocomplete' => 'off',
                ],
                'class' => 'md-input label-fixed',
            ],
            'IssueDateTo' => [
                'name' => 'IssueDateTo',
                'id' => 'IssueDateTo',
                'label' => 'Issue Date To',
                'type' => 'date',
                'extras' => [
                    'placeholder' => '',
                    'data-parsley-trigger' => 'change',
                    'autocomplete' => 'off',
                ],
                'value' => '',
                'class' => 'md-input label-fixed',
            ],
            'VerifyDateFrom' => [
                'name' => 'VerifyDateFrom',
                'id' => 'VerifyDateFrom',
                'label' => 'Verify Date From',
                'type' => 'date',
                'value' => '',
                'extras' => [
                    'placeholder' => '',
                    'data-parsley-trigger' => 'change',
                    'autocomplete' => 'off',
                ],
                'class' => 'md-input label-fixed',
            ],
            'VerifyDateTo' => [
                'name' => 'VerifyDateTo',
                'id' => 'VerifyDateTo',
                'label' => 'Verify Date To',
                'type' => 'date',
                'extras' => [
                    'placeholder' => '',
                    'data-parsley-trigger' => 'change',
                    'autocomplete' => 'off',
                ],
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
                'value' => 'CreateDate',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ],
        ];
        return $fields;
    }

    public function index(Request $request,$mid)
    {
        $pageNumEachPage = 100;                             // 每頁的基本資料量
        $pageNo = (int) $request->input('Page', '1');       // 目前的頁碼
        $IsNewSearch = $request->input('IfNewSearch', '');  // 是否為新開始搜尋
        $IfSearch = $request->input('IfSearch', '');        // 是否為搜尋
        $orderBy = $request->input('orderBy', '');          // 排序欄位
        $isAsc = $request->input('isAsc', '');              // 是否順序排序
        $excel = $request->input('excel', 0);               // 是否匯出excel
        $url = '/Customer/B2B/'.$mid.'/HotspotsList';

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

        $username = DimUser::where('Id',$mid)->select('RealName')->where('IfDelete',0)->first()->RealName;
        $data = DimHotspot::where('IfDelete','0')->where('OwnerID',$mid);

        if ($IfSearch == '1') {
            // 表示會需要參考搜尋的變數
            $searchArray = array(
                'S/N' => $searchFields['S/N']['value'],
                'Mac' => strtolower(str_replace("-",":",$searchFields['Mac']['value'])),
                'AnimalName' => $searchFields['AnimalName']['value'],
                'IsVerify' => $searchFields['IsVerify']['value'],
                'IfRegister' => $searchFields['IfRegister']['value'],
                'IssueDateFrom' => $searchFields['IssueDateFrom']['value'],
                'IssueDateTo' => $searchFields['IssueDateTo']['value'],
                'VerifyDateFrom' => $searchFields['VerifyDateFrom']['value'],
                'VerifyDateTo' => $searchFields['VerifyDateTo']['value'],
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
                if($searchArray['IsVerify'] != '') {
                    $query->where('IsVerify', $searchArray['IsVerify']);
                }
                if($searchArray['IfRegister'] != '') {
                    $query->where('IfRegister', $searchArray['IfRegister']);
                }
                if ($searchArray['IssueDateFrom'] != '') {
                    $query->where('IssueDate', '>=', ($searchArray['IssueDateFrom'] . ' 00:00:00'));
                }
                if ($searchArray['IssueDateTo'] != '') {
                    $query->where('IssueDate', '<=', ( $searchArray['IssueDateTo'] . ' 23:59:59'));
                }
                if ($searchArray['VerifyDateFrom'] != '') {
                    $query->where('IfVerifyDate', '>=', ($searchArray['VerifyDateFrom'] . ' 00:00:00'));
                }
                if ($searchArray['VerifyDateTo'] != '') {
                    $query->where('IfVerifyDate', '<=', ( $searchArray['VerifyDateTo'] . ' 23:59:59'));
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

        // dd($data);
        if ($excel == 1) {
            $path = storage_path('excel/exports/hotspots');
            //dd($path);
            $now = Carbon::now()->format('Y-m-d-H-i-s');

            $excelName = 'Hotspots_' . $now;
            $reports = $data;

            //--- New Excel -------------------------------
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('sheet1');
            // $sheet->setCellValue('A1', 'Hello World !');
            $TitleArray = ['s/n','lan mac','animal name','issue date','verify status','register status'];
            $spreadsheet->getActiveSheet()
                            ->fromArray($TitleArray);
            $data = [];
            if ($reports->count() > 0) {
                $key = 0;
                //foreach start//
                // dd($reports->get());
                foreach ($reports->get() as $report) {
                    $thisRecord = array(
                        $report->DeviceSN,
                        $report->MacAddress,
                        $report->AnimalName,
                        Carbon::parse($report->IssueDate)->format('Y-m-d H:i:s'),
                        // $report->IssueDate,
                    );
                    if($report->IsVerify == 1){
                        $thisRecord[] = Carbon::parse($report->IfVerifyDate)->format('Y-m-d H:i:s');
                        // $thisRecord[] = $report->IfVerifyDate;
                    }else{
                        $thisRecord[] = '';
                    }
                    if($report->IfRegister == 1){
                        $thisRecord[] = 'Y';
                    }else{
                        if($report->IfKey == 1){
                            $thisRecord[] = 'No Animal';
                        }else{
                            if($report->IfAnimal == 1){
                                $thisRecord[] = 'No Key';
                            }else{
                                $thisRecord[] = 'N/A';
                            }
                        }
                    }
                    $data[] = $thisRecord;
                    $key++;
                }
                //foreach end//
            }else{
                $data = ['', '', '', '', '', ''];
            }
            $spreadsheet->getActiveSheet()
                        ->fromArray($data, null, 'A2');

            $writer = new Xlsx($spreadsheet);
            $file = $path . '/' . $excelName . '.xlsx';
            // dd($file);
            $writer->save($file);

            return response()->download($file);
        } else {
            // 分頁
            $data = $data->paginate($pageNumEachPage);
        }

        return view(self::$viewPath.'.index', compact('data'))
                    ->with('i', ($pageNo - 1) * $pageNumEachPage)
                    ->with('username', $username)
                    ->with('url', $url)
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
        // 步驟一：使用預設好的欄位取得資料;
        // 取得輸入欄位的定義
        $formFieldDef = self::defineFormFields();

        // 產生需要設定的欄位  
        $requestResult =  WebLib::generateInputs($formFieldDef, true);
        $formFields = $requestResult["data"];
        $MacAddress = $formFields['MacAddress']['value'];
        $newMacAddress = '';

        // 步驟二：檢查是否有存在相同的值
        if($requestResult['isError'] == false) {
            // for DeviceSN
            $exist = DimHotspot::where('DeviceSN', '=',  $formFields['DeviceSN']['value'])
                                        ->where('IfDelete',0)
                                        ->get();
            
            if($exist->count() > 0 ){
                $requestResult['isError'] = true;
                $formFields['DeviceSN']['isCorrect'] = false;
                $formFields['DeviceSN']['error'] = "This s/n has been taken";
                $formFields['DeviceSN']['completeField'] = GenerateData::generateCustomErrorMessage('s/n','DeviceSN', $formFields['DeviceSN']['value'], $formFields['DeviceSN']['error'], 'text');
            }

            // for MacAddress
            $MacAddress = strtolower(str_replace("-","",$MacAddress));
            $MacAddress = strtolower(str_replace(":","",$MacAddress));
            if(strlen($MacAddress) != 12){
                $requestResult['isError'] = true;
                $formFields['MacAddress']['isCorrect'] = false;
                $formFields['MacAddress']['error'] = "請確認MacAddress長度為12";
                $formFields['MacAddress']['completeField'] = GenerateData::generateCustomErrorMessage('MacAddress','MacAddress', $formFields['MacAddress']['value'], $formFields['MacAddress']['error'], 'text');
            }else{
                for ($i=0; $i < 11; $i+=2) { 
                    $str = substr($MacAddress, $i, 2);
                    if($i != 10){
                        $newMacAddress .= $str.":";
                    }else{
                        $newMacAddress .= $str;
                    }
                }
            }

            $exist = DimHotspot::where('MacAddress', '=',  $newMacAddress)
                                        ->where('IfDelete',0)
                                        ->get();
            
            if($exist->count() > 0 ){
                $requestResult['isError'] = true;
                $formFields['MacAddress']['isCorrect'] = false;
                $formFields['MacAddress']['error'] = "This MacAddress has been taken";
                $formFields['MacAddress']['completeField'] = GenerateData::generateCustomErrorMessage('MacAddress','MacAddress', $formFields['MacAddress']['value'], $formFields['MacAddress']['error'], 'text');
            }

            // for AnimalName
            if( $formFields['AnimalName']['value'] != ''){
                $exist = DimHotspot::where('AnimalName', '=',  $formFields['AnimalName']['value'])
                                            ->where('IfDelete',0)
                                            ->get();
                
                if($exist->count() > 0 ){
                    $requestResult['isError'] = true;
                    $formFields['AnimalName']['isCorrect'] = false;
                    $formFields['AnimalName']['error'] = "This animalname has been taken";
                    $formFields['AnimalName']['completeField'] = GenerateData::generateCustomErrorMessage('animalname','AnimalName', $formFields['AnimalName']['value'], $formFields['AnimalName']['error'], 'text');
                }
            }

                
        }

        // 步驟三：檢查回傳的狀態
        if($requestResult['isError'] == false) {

            // 組成目前需要新增的資料物件;
            $newData = [
                'IssueDate' => Carbon::now('Asia/Taipei')->toDateTimeString(),
                'PalletId' => $formFields['PalletId']['value'],
                'CartonId' => $formFields['CartonId']['value'],
                'DeviceSN' => $formFields['DeviceSN']['value'],
                'MacAddress' => $newMacAddress,
                'AnimalName' => $formFields['AnimalName']['value'],
                'IsVerify' => $formFields['IsVerify']['value'],

                'IfValid' => $formFields['IfValid']['value'],
                'IfDelete' => 0,
                'CreateBy' => WebLib::getCurrentUserID(),
                'CreateDate' => Carbon::now('Asia/Taipei')->toDateTimeString() // 表示為目前時間;
            ];

            // 執行產生資料的動作
            DimHotspot::on('mysql2')->create($newData);

            return redirect()->route(self::$routePath.'.index')
                        ->with('success','成功新增一筆資料！');
        }else{
            // 表示檢查失敗，必須要重新產生要新增的頁面;
            $IfValid= ($formFields['IfValid']['value'] == '') ? 1 : $formFields['IfValid']['value'];
            $formFields['IfValid']['completeField'] = GenerateData::getIfValidHtml($IfValid);

            $formFields['CreateBy']['completeField'] = GenerateData::generateData('CreateBy','CreateBy', 'CreateBy', '', 'text');

            $formFields['CreateDate']['completeField'] = GenerateData::generateData('CreateDate','CreateDate', 'CreateDate', '', 'text');

            return view(self::$viewPath.'.create')
                ->with('formFields', $formFields)
                ->with('routePath', self::$routePath)
                ->with('Action', "NEW")
                ->with('viewPath', self::$viewPath)
                ->with('functionname', self::$functionname)
                ->with('functionURL', self::$functionURL);
        }
    }
    /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function show($id)
    {
        return redirect()->route(self::$routePath.'.index');
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
        $data= DimHotspot::where('id', $id)
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
        $data= DimHotspot::where('id', $id)->first();
        $formFieldDef = $this->defineEditFormFields($data);
        $requestResult = WebLib::generateInputs($formFieldDef, true);
        $formFields = $requestResult["data"];
        $errmsg = "";
        $MacAddress = $formFields['MacAddress']['value'];
        $newMacAddress = '';

        // 檢查是否有存在相同的值
        if($requestResult['isError'] == false) {
            // for DeviceSN
            $exist = DimHotspot::where('DeviceSN', '=',  $formFields['DeviceSN']['value'])
                                        ->where('IfDelete',0)
                                        ->where('id','!=',$id)
                                        ->get();
            
            if($exist->count() > 0 ){
                $requestResult['isError'] = true;
                $formFields['DeviceSN']['isCorrect'] = false;
                $formFields['DeviceSN']['error'] = "This s/n has been taken";
                $formFields['DeviceSN']['completeField'] = GenerateData::generateCustomErrorMessage('s/n','DeviceSN', $formFields['DeviceSN']['value'], $formFields['DeviceSN']['error'], 'text');
            }

            // for MacAddress
            $MacAddress = strtolower(str_replace("-","",$MacAddress));
            $MacAddress = strtolower(str_replace(":","",$MacAddress));
            if(strlen($MacAddress) != 12){
                $requestResult['isError'] = true;
                $formFields['MacAddress']['isCorrect'] = false;
                $formFields['MacAddress']['error'] = "請確認MacAddress長度為12";
                $formFields['MacAddress']['completeField'] = GenerateData::generateCustomErrorMessage('MacAddress','MacAddress', $formFields['MacAddress']['value'], $formFields['MacAddress']['error'], 'text');
            }else{
                for ($i=0; $i < 11; $i+=2) { 
                    $str = substr($MacAddress, $i, 2);
                    if($i != 10){
                        $newMacAddress .= $str.":";
                    }else{
                        $newMacAddress .= $str;
                    }
                }
            }

            $exist = DimHotspot::where('MacAddress', '=',  $newMacAddress)
                                        ->where('IfDelete',0)
                                        ->where('id','!=',$id)
                                        ->get();
            
            if($exist->count() > 0 ){
                $requestResult['isError'] = true;
                $formFields['MacAddress']['isCorrect'] = false;
                $formFields['MacAddress']['error'] = "This MacAddress has been taken";
                $formFields['MacAddress']['completeField'] = GenerateData::generateCustomErrorMessage('MacAddress','MacAddress', $formFields['MacAddress']['value'], $formFields['MacAddress']['error'], 'text');
            }

            // for AnimalName
            if( $formFields['AnimalName']['value'] != ''){
                $exist = DimHotspot::where('AnimalName', '=',  $formFields['AnimalName']['value'])
                                            ->where('IfDelete',0)
                                            ->where('id','!=',$id)
                                            ->get();
                
                if($exist->count() > 0 ){
                    $requestResult['isError'] = true;
                    $formFields['AnimalName']['isCorrect'] = false;
                    $formFields['AnimalName']['error'] = "This animalname has been taken";
                    $formFields['AnimalName']['completeField'] = GenerateData::generateCustomErrorMessage('animalname','AnimalName', $formFields['AnimalName']['value'], $formFields['AnimalName']['error'], 'text');
                }
            }                
        }

        if($requestResult['isError'] == false) {
            $updateValue = [
                'PalletId' => $formFields['PalletId']['value'],
                'CartonId' => $formFields['CartonId']['value'],
                'DeviceSN' => $formFields['DeviceSN']['value'],
                'MacAddress' => $newMacAddress,
                'AnimalName' => $formFields['AnimalName']['value'],
                'IsVerify' => $formFields['IsVerify']['value'],

                'IfValid' => $formFields['IfValid']['value'],
            ];

            if($data->IfValid == '1'&& $formFields['IfValid']['value'] == 0 ) {
                // 表示要變更IfValid狀態由1變成0,需要變更IfNotValidBy跟IfNotValidDate
                $updateValue['IfNotValidBy'] =  WebLib::getCurrentUserID();
                $updateValue['IfNotValidDate'] = Carbon::now('Asia/Taipei')->toDateTimeString();
            }

            DimHotspot::on('mysql2')->where('id', $id)->update($updateValue);
            return redirect()->route(self::$routePath.'.index')
                            ->with('success','更新完成');
        }else{
            //重新產生原本的資料            
            $IfValid= ($formFields['IfValid']['value'] == '') ? $data->IfValid : $formFields['IfValid']['value'];
            $formFields['IfValid']['completeField'] = GenerateData::getIfValidHtml($IfValid);

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
        $CarDeviceEntity = CarDeviceEntity::where('CarId',$id);
        if($CarDeviceEntity->count()>0){
            return redirect()->route(self::$routePath.'.index')
                            ->with('success','資料不可刪除');
        }else{
            $DeleteValue = [
                'IfDelete' => 1, 
                'IfDeleteBy' => WebLib::getCurrentUserID(),
                'DeleteDate' => Carbon::now('Asia/Taipei')->toDateTimeString() //date("Y-m-d H:i:s")
            ];

            DimCar::on('mysql2')->where('CarId', $id)->update($DeleteValue);

            return redirect()->route(self::$routePath.'.index')
                            ->with('success','資料已經刪除');
        }
    }
}