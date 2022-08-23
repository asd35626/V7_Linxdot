<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class HotspotBlackLog extends Model
{
    protected $connection = 'mysql';
    protected $table = 'Hotspot_Black_Log';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
      'id',
      'MacAddress',
      'IsBlack',
      'IsBackMemo',
      'IsBlackBy',
      'IsBlackDate'
    ];
}
