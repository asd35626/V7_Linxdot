<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class KEYHistory extends Model
{
    protected $connection = 'mysql';
    protected $table = 'KEY_Histoty';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public $incrementing = false;
    //const CREATED_AT = 'create_date';
    //const UPDATED_AT = 'last_modified_date';

    protected $fillable = [
      'ID',
      'Key',
      'MemberNo',
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
      'LastModifiedIPAddress'
    ];

    protected $casts = [
      'ID' => 'string',
      'Key' => 'string',
      'MemberNo' => 'string',
      'IfValid' => 'int',
      'IfDelete' => 'int'
    ];
}