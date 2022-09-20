<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LinxdotFirmwareStatus extends Model
{
    protected $connection = 'mysql';
    protected $table = 'Linxdot_Firmware_Status';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
      'id',
      'RecordDate',
      'Firmware',
      'TotalCount',
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
