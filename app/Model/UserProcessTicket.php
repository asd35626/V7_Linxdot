<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserProcessTicket extends Model
{
    //
    protected $connection = 'mysql';
    protected $table = 'UserProcessTicket';
    protected $primaryKey = 'ProcessTicketId';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
      'ProcessTicketId',
      'UID',
      'RequestDate',
      'RequestIPAddress',
      'DeviceType',
      'DeviceInfo',
      'IfSuccess',
      'RequestIssue',
      'IfLogout',      
      'LogoutDate',
      'LogoutIssue',
      'LogoutIPAddress',
      'ChannelId',
      'DeviceToken',
      'ExpireDate',
      'AppName',
      'AppVersionNo'
    ];

    protected $casts = [
      'ProcessTicketId' => 'string',
      'UID' => 'string',
      'ChannelId' => 'string',
      'DeviceToken' => 'string',
      'IfSuccess' => 'int',
      'IfLogout' => 'int',
  ];

    public function user()
    {
       return $this->belongsTo('App\Model\DimUser', 'UID', 'Id');
    }
}
