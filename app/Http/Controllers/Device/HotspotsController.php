<?php
namespace App\Http\Controllers\Device;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use App\Model\DimHotspot;
use Uuid;
use Carbon\Carbon;
use App\V7Idea\WebLib;
use App\V7Idea\GenerateData;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class HotspotsController extends Controller
{
    // 設定blade目錄的位置
    public static $viewPath = "Device.Hotspots";
    
    // 設定route目錄的位置
    public static $routePath = "Hotspots";

    // 這個資料表的主要鍵值
    public static $primaryKey = "CarId";

    // 設定功能名稱
    public static $functionname = "Hotspots";

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
            'CarNumber' => [
                'name' => 'CarNumber',
                'id' => 'CarNumber',
                'label' => '車牌',
                'type' => 'text',
                'validation' => 'required',
                'value' => '',
                'class' => 'md-input label-fixed',
            ],
            'IfValid' => [
                'name' => 'IfValid',
                'id' => 'IfValid',
                'label' => '啟用/停用',
                'type' => 'radio',
                'selectLists' => [
                    '0' => '停用',
                    '1' => '啟用'
                ],
                'value' => '1',
                'validation' => 'required',
                'class' => 'md-input label-fixed',
            ],
            'CreateBy'  => [
                'name' => 'CreateBy',
                'id' => 'CreateBy',
                'label' => '資料建立者',
                'type' => 'text',
                'value' => '系統自動產生',
                'validation' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ],
            'CreateDate' =>  [
                'name' => 'CreateDate',
                'id' => 'CreateDate',
                'label' => '資料建立日期',
                'type' => 'text',
                'validation' => '',
                'value' => '系統自動產生',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ]
        ];
        return $fields;
    }

    public function defineEditFormFields($data) {
        $fields = [
            'CarNumber' => [
                'name' => 'CarNumber',
                'id' => 'CarNumber',
                'label' => '車牌',
                'type' => 'text',
                'validation' => 'required',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ],
            'IfValid' => [
                'name' => 'IfValid',
                'id' => 'IfValid',
                'label' => '啟用/停用',
                'type' => 'radio',
                'selectLists' => [
                    '0' => '停用',
                    '1' => '啟用'
                ],
                'value' => '1',
                'validation' => 'required',
                'class' => 'md-input label-fixed',
            ],
            'CreateBy'  => [
                'name' => 'CreateBy',
                'id' => 'CreateBy',
                'label' => '資料建立者',
                'type' => 'text',
                'value' => '系統自動產生',
                'validation' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ],
            'CreateDate' =>  [
                'name' => 'CreateDate',
                'id' => 'CreateDate',
                'label' => '資料建立日期',
                'type' => 'text',
                'validation' => '',
                'value' => '系統自動產生',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ]
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

        $data = DimHotspot::where('IfDelete','0');

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
                    ->with('IfSearch', $IfSearch)
                    ->with('pageNo', $pageNo)
                    ->with('searchFields',  $searchFields)
                    ->with('routePath', self::$routePath)
                    ->with('viewPath', self::$viewPath)
                    ->with('primaryKey', self::$primaryKey)
                    ->with('orderBy', $orderBy)
                    ->with('isAsc', $isAsc)
                    ->with('pageNumEachPage', $pageNumEachPage)
                    ->with('functionname', self::$functionname);
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
                    ->with('functionname', self::$functionname);
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

        // 步驟二：檢查是否有存在相同的值
        if($requestResult['isError'] == false) {
            // for CarNumber
            $existUserType = DimCar::where('CarNumber', '=',  $formFields['CarNumber']['value'])
                                        ->where('IfDelete',0)
                                        ->get();
            
            if($existUserType->count() > 0 ){
                $requestResult['isError'] = true;
                $formFields['CarNumber']['isCorrect'] = false;
                $formFields['CarNumber']['error'] = "這個車牌已經被用過了，不可以重複";
                $formFields['CarNumber']['completeField'] = GenerateData::generateCustomErrorMessage('車牌','CarNumber', $formFields['CarNumber']['value'], $formFields['CarNumber']['error'], 'text');
            }
        }

        // 步驟三：檢查回傳的狀態
        if($requestResult['isError'] == false) {
            $id = Uuid::generate(4);

            // 組成目前需要新增的資料物件;
            $newData = [
                'CarId' => $id,
                'CarNumber' => $formFields['CarNumber']['value'],

                'IfValid' => $formFields['IfValid']['value'],
                'IfDelete' => 0,
                'CreateBy' => WebLib::getCurrentUserID(),
                'CreateDate' => Carbon::now('Asia/Taipei')->toDateTimeString() // 表示為目前時間;
            ];

            // 執行產生資料的動作
            DimCar::on('mysql2')->create($newData);

            return redirect()->route(self::$routePath.'.index')
                        ->with('success','成功新增一筆資料！');
        }else{
            // 表示檢查失敗，必須要重新產生要新增的頁面;
            $IfValid= ($formFields['IfValid']['value'] == '') ? 1 : $formFields['IfValid']['value'];
            $formFields['IfValid']['completeField'] = GenerateData::getIfValidHtml($IfValid);

            $formFields['CreateBy']['completeField'] = GenerateData::generateData('資料建立者','CreateBy', '系統自動產生', '', 'text');

            $formFields['CreateDate']['completeField'] = GenerateData::generateData('資料建立日期','CreateDate', '系統自動產生', '', 'text');

            return view(self::$viewPath.'.create')
                ->with('formFields', $formFields)
                ->with('routePath', self::$routePath)
                ->with('Action', "NEW")
                ->with('viewPath', self::$viewPath)
                ->with('functionname', self::$functionname);
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
        $data= DimCar::where('CarId', $id)
                            ->where('IfDelete', 0)
                            ->get();

        // 取得輸入欄位的定義
        $formFieldDef = self::defineEditFormFields($data->first());
        // dd($formFieldDef);

        // 把資料放進對應欄位
        $requestResult =  WebLib::generateInputsWhthData($formFieldDef, $data);
        // 修正資料建立者
        $requestResult['CreateBy']['value'] = GenerateData::getCreater($data->first()->CreateBy);
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
                    ->with('functionname', self::$functionname);
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
        $data= DimCar::where('CarId', $id)->first();
        $formFieldDef = $this->defineEditFormFields($data);
        $requestResult = WebLib::generateInputs($formFieldDef, true);
        $formFields = $requestResult["data"];
        $errmsg = "";

        // 檢查是否有存在相同的值
        if($requestResult['isError'] == false) {
        }

        if($requestResult['isError'] == false) {
            $updateValue = [
                'IfValid' => $formFields['IfValid']['value'],
                'LastModifiedBy' => WebLib::getCurrentUserID(),
                'LastModifiedDate' => Carbon::now('Asia/Taipei')->toDateTimeString() // 表示為目前時間;
            ];

            if($data->IfValid == '1'&& $formFields['IfValid']['value'] == 0 ) {
                // 表示要變更IfValid狀態由1變成0,需要變更IfNotValidBy跟IfNotValidDate
                $updateValue['IfNotValidBy'] =  WebLib::getCurrentUserID();
                $updateValue['IfNotValidDate'] = Carbon::now('Asia/Taipei')->toDateTimeString();
            }

            DimCar::on('mysql2')->where('CarId', $id)->update($updateValue);
            return redirect()->route(self::$routePath.'.index')
                            ->with('success','更新完成');
        }else{
            //重新產生原本的資料            
            $IfValid= ($formFields['IfValid']['value'] == '') ? $data->IfValid : $formFields['IfValid']['value'];
            $formFields['IfValid']['completeField'] = GenerateData::getIfValidHtml($IfValid);

            $CreateBy = GenerateData::getCreater($data->first()->CreateBy);
            $formFields['CreateBy']['value'] = $CreateBy;
            $formFields['CreateBy']['completeField'] = GenerateData::generateData('資料建立者','CreateBy', $CreateBy, '', 'text');

            $formFields['CreateDate']['value'] = ($formFields['CreateDate']['value'] == '') ? $data->CreateDate : $formFields['CreateDate']['value'];
            $formFields['CreateDate']['completeField'] = GenerateData::generateData('資料建立日期','CreateDate', $formFields['CreateDate']['value'], '', 'text');

            return view(self::$viewPath.'.edit', compact('data'))
                    ->with('formFields', $formFields)
                    ->with('routePath', self::$routePath)
                    ->with('targetId', $id)
                    ->with('primaryKey', self::$primaryKey)
                    ->with('Action', "EDIT")
                    ->with('viewPath', self::$viewPath)
                    ->with('errmsg', $errmsg)
                    ->with('functionname', self::$functionname);
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