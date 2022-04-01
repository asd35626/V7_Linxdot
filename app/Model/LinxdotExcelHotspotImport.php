<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LinxdotExcelHotspotImport extends Model
{
    protected $connection = 'mysql';
    protected $table = 'Linxdot_Excel_Hotspot_Import';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
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
