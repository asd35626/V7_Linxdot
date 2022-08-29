<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class HotspotMaintainLog extends Model
{
    protected $connection = 'mysql';
    protected $table = 'Hotspot_Maintain_Log';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
      'id',
      'LogId',
      'DeviceSN',
      'MacAddress',
      'LogDate',
      'LogTopic',
      'LogType',
      'LogDescription',
      'IsNeedAssign',
      'AssignTo',
      'ProcessLog',
      'IsCompleted',
      'CompletedReport',
      'IsCompletedBy',
      'IsCompletedDate',
      'IfValid',
      'IfNotValidBy',
      'IfNotValidDate',
      'IfDelete',
      'IfDeleteBy',
      'IfDeleteDate',
      'CreateBy',
      'CreateDate',
      'LastUpdatedBy',
      'LastUpdatedDate'
    ];
}
