<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DimUser extends Model
{
    //
    protected $connection = 'mysql';
    protected $table = 'Dim_User';
    protected $primaryKey = 'Id';
    public $timestamps = false;

    protected $fillable = [
      'Id',
      'MemberNo',
      'RealName',
      'UserPassword',
      'UserEmail',
      'UserType',
      'DegreeId',
      'UserPosition',
      'UserPositionDegree',
      'UserMobile',
      'ContactPhone',
      'ContactAddress',
      'WorkingNumber',
      'MemberStartDate',
      'CompanyName',
      'CompanyNo',
      'CompanyPhone',
      'CompanyFax',
      'CompanyAddress',
      'CompanyEmail',
      'CompanyCapital',
      'CompanyStartDate',
      'InvoiceAddress',
      'ProductDescription',
      'SignDate',
      'UserSex',
      'Memo',
      'IfDelete',
      'IfDeleteBy',
      'IfDeleteDate',
      'IfDeleteIPAddress',
      'IfValid',
      'IfNotValidBy',
      'IfNotValidDate',
      'IfNotValidIPAddress',
      'CreateBy',
      'CreateDate',
      'CreateIPAddress',
      'LastModifiedBy',
      'LastModifiedDate',
      'LastModifiedIPAddress',
      'LoginFailTimes'
    ];

    protected $casts = [
      'Id' => 'string',
      'MemberNo' => 'string',
      'IfValid' => 'int',
      'IfDelete' => 'int',
      'UserType' => 'int',
      'DegreeId' => 'int',
      'LoginFailTimes' => 'int'
  ];

    public function tokens()
    {
        return $this->hasMany('App\Model\UserProcessTicket', 'UID', 'Id')
                ->OrderBy('RequestDate','DESC');
    }

    public function userType()
    {
        return $this->belongsTo('App\Model\DimUserType', 'UserType','UserTypeId');
    }

}
