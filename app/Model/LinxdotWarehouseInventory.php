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
}
