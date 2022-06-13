<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LinxdotExcelWarehouseInventoryImport extends Model
{
    protected $connection = 'mysql';
    protected $table = 'Linxdot_Excel_WarehouseInventory_Import';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
      'id',
      'ImportDate',
      'FilePath',
      'FileName',
      'IfCompleteImport',
      'TotalRecords',
      'IfCompleteVerify',
      'IfCompleteVerifyBy',
      'IfCompleteVerifyDate',
      'CompletedStatus',
      'ErrorCode',
      'ErrorMessage',
      'IfValid',
      'IfNotValidBy',
      'IfNotValidDate',
      'IfDelete',
      'IfDeleteBy',
      'IfDeleteDate',
      'CreateBy',
      'CreateDate'
    ];
}
