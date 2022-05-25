<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DimUserUnlockHistory extends Model
{
    protected $connection = 'mysql';
    protected $table = 'Dim_User_Unlock_History';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
      'ID',
      'UID',
      'CreateBy',
      'CreateDate',
      'CreateIPAddress',
    ];

    protected $casts = [
      'ID' => 'string',
      'UID' => 'string'
    ];

    public function User(){
      return $this->belongsTo('App\Model\DimUser', 'UID', 'Id');
    }
}
