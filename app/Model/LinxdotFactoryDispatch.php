<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LinxdotFactoryDispatch extends Model
{
    protected $connection = 'mysql';
    protected $table = 'Linxdot_Factory_Dispatch';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
      'id',
      'IssueDate',
      'SkuID',
      'PalletID',
      'CartonID',
      'DeviceSN',
      'MacAddress',
      'WifiMac',
      'HWModelNo',
      'RegsionID',
      'FactoryID',
      'WarehouseID',
      'IfValid',
      'IfNotValidBy',
      'IfNotValidDate',
      'IfDelete',
      'IfDeleteBy',
      'IfDeleteDate',
      'CreateBy',
      'CreateDate'
    ];

    // 機器本體
    public function Hotspot()
    {
        return $this->belongsTo('App\Model\DimHotspot', 'MacAddress','MacAddress');
    }
    // 製造商
    public function Factory()
    {
        return $this->belongsTo('App\Model\DimUser', 'FactoryID','Id');
    }
}
