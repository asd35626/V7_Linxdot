<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DimUserType extends Model
{
    //
    protected $connection = 'mysql';
    protected $table = 'Dim_UserType';
    protected $primaryKey = 'UserTypeId';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
      'UserTypeId',
      'UserTypeName',
      'UserTypeMemo',
      'UserTypeMark',
      'IfValid',
      'IfDelete',
      'CreateBy',
      'CreateDate',
      'UpdateBy',
      'UpdateDate',
      'IfDeleteBy',
      'DeleteDate'
    ];

    public function users()
    {
        return $this->hasMany('App\Model\DimUser', 'UserTypeId','UserType');
    }
}
