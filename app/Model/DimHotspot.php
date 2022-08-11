<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DimHotspot extends Model
{
    protected $connection = 'mysql';
    protected $table = 'Dim_Hotspot';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
      'id',
      'IssueDate',
      'PalletId',
      'CartonId',
      'DeviceSN',
      'MacAddress',
      'Firmware',
      'AnimalName',
      'OnBoardingKey',
      'DeviceID',
      'WifiMacAddress',
      'MgrVersion',
      'BlockHeight',
      'MinerVersion',
      'IsVerify',
      'IfVerifyDate',
      'VerifyMemo',
      'IfGetName',
      'IfGetKey',
      'IfRegister',
      'IfOnline',
      'LastUpdateOnLineTime',
      'DewiStatus',
      'ActiveTime',
      'IsShipped',
      'ShippedDate',
      'TrackNo',
      'ManufactureID',
      'WarehouseID',
      'OwnerID',
      'P2P_Connected',
      'P2P_Dialable',
      'P2P_NatType',
      'Region',
      'IfUpdateLocation',
      'UpdateLocationMemo',
      'map_lat',
      'map_lng',
      'IsRegisteredDewi',
      'LastRegisterDewiDate',
      'LastRegisterDewiStatus',
      'LastRegisterDewiCode',
      'LastRegisterDewiMemo',
      'CurrentMacAddress',
      'IfValid',
      'IfNotValidBy',
      'IfNotValidDate',
      'IfDelete',
      'IfDeleteBy',
      'IfDeleteDate',
      'CreateBy',
      'CreateDate',
      'create_time',
      'update_time',
      'IsBlack',
      'IsBackMemo',
      'IsBlackBy',
      'IsBlackDate',
      'NickName',
      'OfficalNickName'
    ];

    // 生產者
    public function Manufacturer()
    {
        return $this->belongsTo('App\Model\DimUser', 'ManufactureID','Id');
    }
    // 分位
    public function Version()
    {
        return $this->belongsTo('App\Model\DimFirmware', 'Firmware','Version Code');
    }

    // 出貨資訊
    public function Warehouse()
    {
        return $this->belongsTo('App\Model\LinxdotWarehouseInventory', 'MacAddress','MacAddress');
    }

    // 擁有者
    public function Owner()
    {
        return $this->belongsTo('App\Model\DimUser', 'OwnerID','Id');
    }
}
