<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LinxdotExcelWarehouseInventoryLog extends Model
{
    protected $connection = 'mysql';
    protected $table = 'Linxdot_Excel_WarehouseInventory_Log';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
      'id',
      'DeviceSN',
      'MacAddress',
      'SkuID',
      'IfShipped',
      'ShippedDate',
      'TrackingNo',
      'NewSkuID',
      'NewIfShipped',
      'NewShippedDate',
      'NewTrackingNo',
      'CreateBy',
      'CreateDate'
    ];
}
