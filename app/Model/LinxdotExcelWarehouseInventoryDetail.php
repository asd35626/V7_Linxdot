<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LinxdotExcelWarehouseInventoryDetail extends Model
{
    protected $connection = 'mysql';
    protected $table = 'Linxdot_Excel_WarehouseInventory_Detail';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
      'id',
      'ImportID',
      'SkuID',
      'PalletId',
      'CartonId',
      'DeviceSN',
      'MacAddress',
      'Location',
      'ShippedStatus',
      'IfShipped',
      'ShippedDate',
      'TrackingNo',
      'IfCompletedImport',
      'ImportStatus',
      'ImportMemo',
      'IfCompletedImportDate',
      'CreateBy',
      'CreateDate'
    ];
}
