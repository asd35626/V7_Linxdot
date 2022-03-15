<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AdminDefaultPermission extends Model
{
    //
    protected $connection = 'mysql';
    protected $table = 'ADM_DefaultPermission';
    protected $primaryKey = 'PermissionId';
    public $incrementing = false;
    public $timestamps = false;
    
    protected $fillable = [
      'PermissionId',
      'UserTypeId',
      'UserDegreeId',
      'FunctionId',
      'IfAccess',
      'CreateBy',
      'CreateDate',
    ];

    protected $casts = [
      'PermissionId' => 'string',
  ];

    public function functionList()
    {
        return $this->hasOne('App\Model\AdminFunction', 'FunctionId', 'FunctionId');
    }
}
