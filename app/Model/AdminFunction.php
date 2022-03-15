<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AdminFunction extends Model
{
    //
    protected $connection = 'mysql';
    protected $table = 'ADM_Function';
    protected $primaryKey = 'FunctionId';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
      'FunctionId',
      'FunctionName',
      'FunctionDesc',
      'FunctionURL',
      'FunctionCode',
      'ParentFunctionId',
      'IfValid',
      'IfDelete',
      'MenuOrder',
      'CreateBy',
      'CreateDate',
      'IfNotValidBy',
      'IfNotValidDate',
      'UpdateBy',
      'UpdateDate',
      'IfDeleteBy',
      'IfDeleteDate',
    ];
    // public function permission()
    // {
    //     return $this->belongsTo('App\AdminDefaultPermission', 'FunctionId', 'FunctionId');
    // }

    public function parent() {

      return $this->belongsTo('App\Model\AdminFunction',  'ParentFunctionId', 'FunctionId')->get()->first();

    }
}
