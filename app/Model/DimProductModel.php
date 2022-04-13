<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DimProductModel extends Model
{
    protected $connection = 'mysql';
    protected $table = 'Dim_ProductModel';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
      'id',
      'ModelID',
      'ModelNo',
      'ModelName',
      'ModelSpec',
      'ModelInfo',
      'IfValid',
      'IfNotValidBy',
      'IfNotValidDate',
      'IfDelete',
      'IfDeleteBy',
      'IfDeleteDate',
      'CreateBy',
      'CreateDate',
      'UpdateBy',
      'UpdateDate'
    ];
}
