<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DimUserDegreeToUserType extends Model
{
    //
    protected $connection = 'mysql';
    protected $table = 'Dim_UserDegreeToUserType';
    protected $primaryKey = 'UTID';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
      'UTID',
      'UserType',
      'DegreeId',
      'DegreeName',
      'DegreeMemo',
      'Code',
      'IfValid',
      'IfDelete',
      'IfNotValidBy',
      'IfNotValidDate',
      'CreateBy',
      'CreateDate',
      'UpdateBy',
      'UpdateDate',
      'IfDeleteBy',
      'DeleteDate'
    ];

    protected $casts = [
      'UTID' => 'string',
      'IfValid' => 'int',
      'IfDelete' => 'int',
      'DegreeId' => 'int',
      'UserType' => 'int',
    ];

    public function userType() {
    
      return $this->belongsTo('App\Model\DimUserType',  'UserType', 'UserTypeId')->get()->first();
       
    }
}
