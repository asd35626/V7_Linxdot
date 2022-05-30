<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LinxdotExcelFactoryDispatchDetail extends Model
{
    protected $connection = 'mysql';
    protected $table = 'Linxdot_Excel_FactoryDispatch_Detail';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
      'id',
      'ImportID',
      'IssueDate',
      'SkuID',
      'PalletId',
      'CartonId',
      'DeviceSN',
      'MacAddress',
      'WifiMac',
      'HWModelNo',
      'RegionID',
      'OuterCasingColor',
      'IfCompletedImport',
      'ImportStatus',
      'ImportMemo',
      'IfCompletedImportDate',
      'CreateBy',
      'CreateDate'
    ];
}
