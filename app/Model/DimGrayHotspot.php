<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DimGrayHotspot extends Model
{
    protected $connection = 'mysql';
    protected $table = 'Dim_GrayHotspot';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;
    
    protected $fillable = [
      'id',
      'AnimalName',
      'OnBoardingKey',
      'DeviceSN',
      'MacAddress',
      'WifiMac',
      'MgrVersion',
      'MinerVersion',
      'Firmware',
      'Region',
      'LastUpdateTime',
      'IsFixed',
      'HotspoType',
      'GrayMemo',
      'UpdatedBy',
      'UpdatedDate',
      'IfValid',
      'IfNotValidBy',
      'IfNotValidDate',
      'IfDelete',
      'IfDeleteBy',
      'IfDeleteDate',
      'CreateBy',
      'CreateDate'
    ];
    // 分位
    public function Version()
    {
        return $this->belongsTo('App\Model\DimFirmware', 'Firmware','Version Code');
    }
}
