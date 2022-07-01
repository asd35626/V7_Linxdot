<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DimFirmware extends Model
{
    protected $connection = 'mysql';
    protected $table = 'Dim_Firmware';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
      'id',
      'FirmwareID',
      'VersionNo',
      'Version Code',
      'ProductModelID',
      'ReleaseDate',
      'ImageDownloadURL',
      'SysupgradeURL',
      'IfRelease',
      'ReleaseMemo',
      'IfValid',
      'IfNotValidBy',
      'IfNotDalidDate',
      'IfDelete',
      'DeleteBy',
      'DeleteDate',
      'LastUpdatedBy',
      'LastUpdatedDate'
    ];
}
