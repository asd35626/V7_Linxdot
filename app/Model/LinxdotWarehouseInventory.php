<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LinxdotWarehouseInventory extends Model
{
    protected $connection = 'mysql';
    protected $table = 'Linxdot_Warehouse_Inventory';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
      'id',
      'WarehouseID',
      'SkuID',
      'PalletID',
      'CatronID',
      'DeviceSN',
      'MacAddress',
      'Location',
      'ShippedStatus',
      'IfShipped',
      'ShippedDate',
      'TrackingNo',
      'IfValid',
      'IfNotValidBy',
      'IfNotValidDate',
      'IfDelete',
      'IfDeleteBy',
      'IfDeleteDate',
      'CreateBy',
      'CreateDate',
      'LastModifyBy',
      'LastModifyDate',
      'LastModifiedByRecordID'
    ];
    // 機器本體
    public function Hotspot()
    {
        return $this->belongsTo('App\Model\DimHotspot', 'MacAddress','MacAddress');
    }
    // 物流商
    public function Warehouse()
    {
        return $this->belongsTo('App\Model\DimUser', 'WarehouseID','Id');
    }
    // B2B會員
    public function B2B()
    {
        return $this->belongsTo('App\Model\DimUser', 'CustomInfo','Id');
    }
}
