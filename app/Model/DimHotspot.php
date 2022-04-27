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
      'IsVerify',
      'IfVerifyDate',
      'IfGetName',
      'IfGetKey',
      'IfRegister',
      'IfOnline',
      'LastUpdateOnLineTime',
      'DewiStatus',
      'IsShipped',
      'ShippedDate',
      'TrackNo',
      'ManufactureID',
      'WarehouseID',
      'IfValid',
      'IfNotValidBy',
      'IfNotValidDate',
      'IfDelete',
      'IfDeleteBy',
      'IfDeleteDate',
      'CreateBy',
      'CreateDate',
      'OwnerID'
    ];
}
