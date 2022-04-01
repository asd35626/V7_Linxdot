<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LinxdotExcelHotspotDetail extends Model
{
    protected $connection = 'mysql';
    protected $table = 'Linxdot_Excel_Hotspot_Detail';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
      'id',
      'ImportID',
      'IssueDate',
      'PalletId',
      'CartonId',
      'DeviceSN',
      'MacAddress',
      'Firmware',
      'IfCompletedImport',
      'ImportStatus',
      'ImportMemo',
      'IfCompletedImportDate',
      'CreateBy',
      'CreateDate'
    ];
}
