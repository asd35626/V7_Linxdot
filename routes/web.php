<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/Default', 'IndexController@index')->name('Default.index');

Route::redirect('/', '/Admin/Login', 302);
Route::get('/Admin/Login', function () {
    return view('AdminLogin');
});

Route::resource('test', 'testController');

// For Login
Route::group(['prefix' => 'api/v1'],function () {
    // 登入檢查
    Route::resource('UserProcessTickets', 'Login\UserProcessTicketsController');
    Route::post('UserProcessKey','Login\UserProcessKeyController@store');
    // 取得用戶等級權限表
    Route::post('System/UserSetting/Permission/List', 'SystemPermissionAPIController@PermissionList');
    // 開啟/關閉權限
    Route::post('System/UserSetting/Permission/TurnOn', 'SystemPermissionAPIController@TurnOn');
    Route::post('System/UserSetting/Permission/TurnOff', 'SystemPermissionAPIController@TurnOff');

    // 圖片上傳api
    Route::resource('UploadImage', 'API\V1\UploadImageController');

    // 會員功能
    // update 發言權
    Route::post('ChangeSpeakAuth', 'Member\MemberManagementController@changeSpeakAuth');
    // 解除鎖定
    Route::post('User/Unlock', 'User\UserController@Unlock');
    // 顯示會員2050
    Route::post('showUserList', 'Device\HotspotsController@showUserList');

    //B2B
    // 更改暱稱
    Route::post('B2BUpdateNickName', 'B2B\DashboardController@updateNickName');

    // 機器相關
    // 顯示匯入清單
    Route::post('WarehouseInventoryDetail', 'Inventory\ShippedStatusExcelController@WarehouseInventoryDetail');
    // 顯示匯入清單
    Route::post('WarehouseInventoryDetail2', 'Inventory\ShippedStatusExcel2Controller@WarehouseInventoryDetail');
    // 更改所屬會員
    Route::post('updateUID', 'Device\HotspotsController@updateUID');
    // 更改黑名單狀態
    Route::post('updateIsBlack', 'Device\HotspotsController@updateIsBlack');
    // 更改暱稱
    Route::post('UpdateNickName', 'Device\HotspotsController@updateNickName');
    // 取得onlinetime
    Route::post('GetOnlineTime', 'Device\HotspotsController@getOnlineTime');
    // Block
    Route::post('Block', 'Device\HotspotsController@block');
    // 回報問題
    Route::post('UpdateIssue', 'Device\HotspotsController@updateIssue');    
    // 取得所有機器(匯出)
    Route::post('GetHotspot', 'Device\HotspotsController@getHotspot');
    // RegisteredDewi前應做的修改
    Route::post('RegisteredDewi', 'Device\HotspotsController@registeredDewi');

    // 處理問題
    Route::post('SolvingIssue', 'CustomerService\IssuesController@solvingIssue');

    // 首頁
    // Firmware機器列表
    Route::post('ShowFirmwareList', 'IndexController@showFirmwareList');
    // Miner機器列表
    Route::post('ShowMinerList', 'IndexController@showMinerList');
});

//帳號相關API
Route::group(['prefix' => 'api/v1', 'middleware' => ['token']],function () {
    //後台帳號管理->取得該群組所屬身份列表
    Route::post('GetUserDegreeList', 'System\SystemAccountManagementController@GetUserDegreeListAPI');
    //後台帳號管理->mail 新密碼
    Route::post('SendNewPassword', 'System\SystemAccountManagementController@SendNewPasswordAPI');
    Route::resource('AdminDefaultPermission', 'AdminDefaultPermissionsController');
});

Route::group(['prefix' => 'files'], function () {
    // 顯示圖片
    Route::get('{folder}/{filename}', 'FileController@getFile')->where('filename', '^[^/]+$');
});


//系統設定相關頁面
Route::group(['prefix' => 'System/UserSetting'],function () {
    //用戶型別管理
    Route::resource('UserType', 'System\SystemUserTypeController');
    //用戶身分管理
    Route::resource('UserDegreeId', 'System\SystemUserDegreeToUserTypesController');
    //上層選單設定
    Route::resource('TopMenu', 'System\SystemTopMenuController');
    //左方子選單設定
    Route::resource('LeftMenu', 'System\SystemLeftMenuController');
    //用戶等級權限表
    Route::resource('Permission', 'System\SystemPermissionController');
    //後台帳號管理
    Route::resource('AccountManagement', 'System\SystemAccountManagementController');
});

//設備管理
Route::group(['prefix' => 'Device'],function () {
    //
    Route::resource('Hotspots', 'Device\HotspotsController');
    //型號管理
    Route::resource('ProductModel', 'Device\ProductModelController');
    //excel上傳
    Route::resource('Excel', 'Device\ExcelController');
    //B2B會員設備管理
    Route::resource('B2BHotspots', 'Device\B2BHotspotsController');
    //
    Route::resource('GrayHotspot', 'Device\GrayHotspotController');
    //
    Route::resource('OperationSummary', 'Device\OperationSummaryController');
});

//人員管理
Route::group(['prefix' => 'Fulfillment'],function () {
    // 物流商管理
    Route::resource('Warehouse', 'Fulfillment\WarehouseController');
    // 製造商管理
    Route::resource('Manufacturer', 'Fulfillment\ManufacturerController');
});

//設備管理
Route::group(['prefix' => 'Inventory'],function () {
    //工廠設備管理
    Route::resource('FactoryDispatch', 'Inventory\FactoryDispatchController');
    //工廠設備匯入管理
    Route::resource('FactoryDispatchExcel', 'Inventory\FactoryDispatchExcelController');
    //工廠設備管理
    Route::resource('ShippedStatus', 'Inventory\ShippedStatusController');
    //工廠設備匯入管理
    Route::resource('ShippedStatusExcel', 'Inventory\ShippedStatusExcelController');
    //工廠設備匯入管理
    Route::resource('ShippedStatusExcel2', 'Inventory\ShippedStatusExcel2Controller');
});


//客戶服務
Route::group(['prefix' => 'CustomerService'],function () {
    //回報問題管理
    Route::resource('Issues', 'CustomerService\IssuesController');
});

//會員管理
Route::group(['prefix' => 'Customer'],function () {
    // B2B會員
    Route::resource('B2B', 'Customer\B2BController');
    // B2B會員的hotspots列表
    Route::resource('B2B/{ID}/HotspotsList', 'Customer\HotspotsListController', [
        'names' => [
            'index' => 'Customer.B2B.HotspotsList.index'
        ]
    ]);
});

//會員使用畫面
Route::group(['prefix' => 'B2B'],function () {
    //地圖畫面
    Route::resource('Map', 'B2B\MapController');
    //設備管理
    Route::resource('Dashboard', 'B2B\DashboardController');
});

//修改後台帳密碼(右上角的個人選單)
Route::resource('/Profile/PasswordSetting', 'ProfilePasswordSettingController');
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
