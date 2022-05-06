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
      'IfValid',
      'IfNotValidBy',
      'IfNotValidDate',
      'IfDelete',
      'IfDeleteBy',
      'IfDeleteDate',
      'CreateBy',
      'CreateDate',
      'create_time',
      'update_time'
    ];
}
