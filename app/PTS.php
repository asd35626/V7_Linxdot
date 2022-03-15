<?php

namespace App;

use Carbon\Carbon;
use Uuid;

use App\Model\PTSSource;
use App\Model\PTSUser;
use App\Model\DimUser;
use App\Model\PTSTransactionTag;
use App\Model\PTSTransProcessHistory;

class PTS
{
    protected $responseBody = array(
        'status' => 0,
        'message' => '',
        'ErrorID' => 0,
    );
    protected $UserID = "";
    protected $UserAuth = "";
    protected $SourceID = "";
    protected $SourcePtsUserID = "";
    protected $SourceExchangeRate = 0;
    function __construct($SourceName = '', $SourceAuth = '', $UserID='', $UserAuth='')
    {
        //check Source Name and Auth
        if ($SourceName == "" || $SourceAuth == "") {
            $this->responseBody['status'] = 1;
            $this->responseBody['ErrorID'] = 99;
            $this->responseBody['data'] = [
                'SourceName' => $SourceName,
                'SourceAuth' => $SourceAuth,
            ];
        } else {
            $PTSSource = PTSSource::select('SourceID', 'PtsUserID', 'ExchangeRate')->where('SourceName', $SourceName)->where('SourceAuth', $SourceAuth);
            if ($PTSSource->count() != 0) {
                $PTSSource= $PTSSource->first();
                $this->SourceID = $PTSSource['SourceID'];
                $this->SourcePtsUserID = $PTSSource['PtsUserID'];
                $this->SourceExchangeRate = $PTSSource['ExchangeRate'];
                // dd($PTSSource);
            } else {
                $this->responseBody['status'] = 1;
                $this->responseBody['ErrorID'] = 1;
            }
        }
        //check User ID and Auth
        if ($this->responseBody['status'] == 0) {
            if ($UserID != "" && $UserAuth != "") {
                $PTSUser = PTSUser::where('PtsUserID', $UserID)->where('PtsUserAuth', $UserAuth)->where('SourceID', $this->SourceID)->where('PtsUserStatus', 1);
                // dd($UserID.','.$UserAuth.','.$this->SourceID);
                // dd($PTSUser->count());
                if ($PTSUser->count() != 0) {
                    $this->UserID = $UserID;
                    $this->UserAuth = $UserAuth;
                } else {
                    $this->responseBody['status'] = 1;
                    $this->responseBody['message'] = "User does not exist";
                    $this->responseBody['ErrorID'] = 2;
                    $this->responseBody['data'] = [
                        'SourceName' => $SourceName,
                        'SourceAuth' => $SourceAuth,
                        'UserID' => $UserID,
                        'UserAuth' => $UserAuth,
                    ];
                }
            } else {
                $this->responseBody['status'] = 1;
                $this->responseBody['message'] = "User ID and Auth is null";
                $this->responseBody['ErrorID'] = 2;
            }
        }
    }

    public function SetPassword()
    {
        return str_pad((string)rand(0, 99999999), 8, '0', STR_PAD_LEFT);
    }

    public function CreateUser($PtsSourceUserID = '')
    {
        // CreateUser 不用檢查PTSUser 是否存在，所以將responseBody 改回預設
        $this->responseBody = array(
            'status' => 0,
            'message' => '',
            'ErrorID' => 0,
        );

        $NewPtsUserID = "";
        $ReturnXMLString = "";
        $UserPassword = "";
        $SubSQL = "";
        $NewConnection;
        $RS;
        $RSReader;

        if ($PtsSourceUserID == "") {
            $this->responseBody['status'] = 1;
            $this->responseBody['ErrorID'] = 99;
        }

        if ($this->responseBody['status'] == 0) {
            $PTSUser = PTSUser::select('PtsUserID')->where('SourceID', $this->SourceID)->where('PtsSourceUserID', $PtsSourceUserID);
            if ($PTSUser->count() != 0) {
                $PTSUser = $PTSUser->first();
                $this->responseBody['status'] = 1;
                $this->responseBody['message'] = 'SourceUser is exist';
                $this->responseBody['ErrorID'] = 1;
            }
        }
        
        if ($this->responseBody['status'] == 0) {
            $UserPassword = $this->SetPassword();
            $NewPtsUserID = Uuid::generate(4);

            $storeArray = array(
                'PtsUserID' => $NewPtsUserID->string,
                'PtsUserAuth' => $UserPassword,
                'PtsSourceUserID' => $PtsSourceUserID,
                'SourceID' => $this->SourceID,
                'PtsUserType' => '0',
                'PtsUserStatus' => '1',
                'CreateBy' => $PtsSourceUserID,
                'CreateDate' => Carbon::now('Asia/Taipei'),
            );
            // dd($storeArray);
            PTSUser::create($storeArray);

            $this->responseBody['PtsUserID'] = $NewPtsUserID->string;
            $this->responseBody['PtsUserAuth'] = $UserPassword;
            //dd($this->responseBody);
        }

        return $this->responseBody;
    }

    public function CreatePtsTransProcess($TagID = '', $ProcessNum = '', $Type = '', $Reason = '', $TransDate = '', $CreateBy = '', $SalesCode = null)
    {
        $SourcePtsUserID = "";
        $SourceID = "";
        $UserAwardedPoints = 0;
        $UserRemainingPoints = 0;
        $UserPendingPoints = 0;
        $UserSpendPoints = 0;
        $SourceAwardedPoints = 0;
        $SourcePendings = 0;
        $SourceSpendPoints = 0;
        $TagName = "";
        $TagType = "";
        $TransactionType = "";
        $TagPoints = 0;
        $IfUnlimited = 0;
        $TagTotalPoints = 0;
        $IfFrequency = "";
        $FrequencyID = "";
        $TagEndOfDate = "";
        $CheckFrequency = 0;
        $CheckQuota = 0;
        // dd('debug:'.$TagID.','.$ProcessNum.','.$TransDate);
        //check TransDate and ProcessNum
        if ($this->responseBody['status'] == 0) {
            $TransDate = Carbon::createFromFormat('Y-m-d H:i:s', $TransDate);
            if ($TransDate === false || is_nan($ProcessNum)) {
                $this->responseBody['status'] = 1;
                $this->responseBody['ErrorID'] = 98;
                $this->responseBody['data'] = ['ProcessNum' => $ProcessNum, 'TransDate' => $TransDate];
            }
        }
        //Get user totalpoints
        if ($this->responseBody['status'] == 0) {
            $AawardedPoints = "";
            $SpendPoints = "";
            $PendingPoints = "";
            $GetResult = $this->Points('total');
            // dd($GetResult);
            if ($GetResult != "") {
                if ($GetResult['status'] == 0) {
                    //Get The user true remaining points
                    $RemainingPoints = $GetResult['RemainingPoints'];
                    if ($RemainingPoints != "" && !is_nan($RemainingPoints)) {
                        $UserRemainingPoints = $RemainingPoints;
                    }
                    //Get The user true spend points
                    $SpendPoints = $GetResult['SpendPoints'];
                    if ($SpendPoints != "" && !is_nan($SpendPoints)) {
                        $UserSpendPoints = $SpendPoints;
                    }
                    //Get The user true pending points
                    $PendingPoints = $GetResult['PendingPoints'];
                    if ($PendingPoints != "" && !is_nan($PendingPoints)) {
                        $UserPendingPoints = $PendingPoints;
                    }
                }
            }
        }
        //check Tag if it is legal and not expire
        if ($this->responseBody['status'] == 0) {
            if ($TagID == "") {
                $this->responseBody['status'] = 1;
                $this->responseBody['ErrorID'] = 99;
            } else {
                $PTSTransactionTag = PTSTransactionTag::select('TagName', 'TagType', 'TransactionType', 'TagPoints', 'IfUnlimited', 'TagTotalPoints', 'IfFrequency', 'FrequencyID', 'TagEndOfDate')->where('TTID', $TagID)->where('TagSource', $this->SourceID)->where('TagStatus', 1)->where('TagEndOfDate', '>', Carbon::now()->format('Y-m-d H:i:s'));
                if ($PTSTransactionTag->count() == 0) {
                    //$PTSTransactionTag= $PTSTransactionTag->first();
                    $this->responseBody['status'] = 1;
                    $this->responseBody['ErrorID'] = 3;
                } else {
                    $PTSTransactionTag= $PTSTransactionTag->first();
                    $TagName = $PTSTransactionTag['TagName'];
                    $TagType = $PTSTransactionTag['TagType'];
                    $TransactionType = $PTSTransactionTag['TransactionType'];
                    $TagPoints = $PTSTransactionTag['TagPoints'];
                    $IfUnlimited = $PTSTransactionTag['IfUnlimited'];
                    $TagTotalPoints = $PTSTransactionTag['TagTotalPoints'];
                    $IfFrequency = $PTSTransactionTag['IfFrequency'];
                    $FrequencyID = $PTSTransactionTag['FrequencyID'];
                    $TagEndOfDate = $PTSTransactionTag['TagEndOfDate'];
                    // dd($PTSTransactionTag);
                    if ($IfFrequency == 1) {
                        $CheckFrequency = IsCheckFrequency($this->UserID, $TagID, $FrequencyID, $ProcessNum); //0:Error, 1:Success
                    }
                    if ($IfUnlimited == 0) {
                        $CheckQuota = IsCheckQuota($TagID, $ProcessNum); //0:Error, 1:Success
                    }
                }
            }
        }
        //check if User have enough points
        if ($this->responseBody['status'] == 0 && ($UserRemainingPoints+($TagPoints*$ProcessNum)) < 0) {
            $this->responseBody['status'] = 1;
            $this->responseBody['ErrorID'] = 5;
        }
        if ($this->responseBody['status'] == 0 && $CheckFrequency == 1 && $CheckQuota == 1) {
            if (is_nan($ProcessNum)) {
                $ProcessNum = 1;
            }
        }
        // Add the Point record to history
        $UserProcessID = Uuid::generate(4);
        if ($this->responseBody['status'] == 0) {
            $SourceProessID = Uuid::generate(4);

            // Add the redeem history to PTS_TransProcessHistory Table
            $storeArray = array(
                'ProcessID' => $UserProcessID->string,
                'PtsUserID' => $this->UserID,
                'TTID' => $TagID,
                'TransactionDescription' => 'N'.$TagName,
                'TransDate' => $TransDate,
                'NumberOfTrans' => $ProcessNum,
                'ExchangeRate' => $this->SourceExchangeRate,
                'TransPts' => $TagPoints,
                'TotalPoints' => ($ProcessNum*$TagPoints*$this->SourceExchangeRate),
                'TransRelatedID' => $SourceProessID->string,
                'TransactionType' => $TransactionType,
                'ProcessDate' => Carbon::now('Asia/Taipei'),
                'TransStatus' => 1,
                'Reason' => $Reason,
                'SalesCode' => $SalesCode,
                // 'CreateBy' => $this->UserID,
                'CreateBy' => ($CreateBy != '') ? $CreateBy : '系統管理員',
                'CreateDate' => Carbon::now('Asia/Taipei'),
            );
            PTSTransProcessHistory::create($storeArray);

            //Response.Write SubSql
            $storeArray = array(
                'ProcessID' => $SourceProessID->string,
                'PtsUserID' => $this->SourcePtsUserID,
                'TTID' => $TagID,
                'TransactionDescription' => 'N'.$TagName,
                'TransDate' => $TransDate,
                'NumberOfTrans' => -$ProcessNum,
                'ExchangeRate' => $this->SourceExchangeRate,
                'TransPts' => $TagPoints,
                'TotalPoints' => -($ProcessNum*$TagPoints*$this->SourceExchangeRate),
                'TransRelatedID' => $UserProcessID->string,
                'TransactionType' => $TransactionType,
                'ProcessDate' => Carbon::now('Asia/Taipei'),
                'TransStatus' => 1,
                'Reason' => $Reason,
                'SalesCode' => $SalesCode,
                // 'CreateBy' => $this->UserID,
                'CreateBy' => ($CreateBy != '') ? $CreateBy : '系統管理員',
                'CreateDate' => Carbon::now('Asia/Taipei'),
            );
            PTSTransProcessHistory::create($storeArray);
            //Update PtsUser and SourceUser's Awarded and Pening points.
            switch ((int)$TransactionType) {
                case 0:
                    $UserRemainingPoints = $UserRemainingPoints + ($ProcessNum * $TagPoints);
                    break;
                case 1:
                    $UserPendingPoints = $UserPendingPoints + ($ProcessNum * $TagPoints);
                    break;
            }
            if ($TagType == 0) {
                $UserSpendPoints = abs($UserSpendPoints) + abs($ProcessNum * $TagPoints);
            }
            $PTSUser = PTSUser::find($this->UserID);
            $PTSUser->AwardedPoints = $UserRemainingPoints;
            $PTSUser->PendingPoints = $UserPendingPoints;
            $PTSUser->SpendPoints = $UserSpendPoints;
            $PTSUser->save();

            $this->responseBody['TransactionProcessID'] = $UserProcessID->string;
        }

        //更新回傳的點數數值
        $this->Points('total');
        
        return $this->responseBody;
    }

    // PointType: total=全部, remaining=剩餘, awarded=獲得, spend=花費, pending=處理中
    public function Points($PointType='')
    {
        if ($this->responseBody['status'] == 0) {
            $SubTotalPoints = PTSTransProcessHistory::select('TotalPoints')->where('PtsUserID', $this->UserID)->where('TransStatus', 1)->where('IfDelete', 0);
            switch ($PointType) {
                case 'remaining':
                    $SubTotalPoints = $SubTotalPoints->where('TransactionType', 0);
                    break;
                case 'awarded':
                    $SubTotalPoints = $SubTotalPoints->where('TransactionType', 0)->where('TotalPoints', '>', 0);
                    break;
                case 'spend':
                    $SubTotalPoints = $SubTotalPoints->where('TransactionType', 0)->where('TotalPoints', '<', 0);
                    break;
                case 'pending':
                    $SubTotalPoints = $SubTotalPoints->where('TransactionType', 1);
                    break;
                
                case 'total':
                    $RemainingPoints = PTSTransProcessHistory::select('TotalPoints')
                                    ->where('PtsUserID', $this->UserID)
                                    ->where('TransStatus', 1)
                                    ->where('TransactionType', 0)
                                    ->where('IfDelete', 0);
                    $AwardedPoints = PTSTransProcessHistory::select('TotalPoints')
                                    ->where('PtsUserID', $this->UserID)
                                    ->where('TransStatus', 1)
                                    ->where('TransactionType', 0)
                                    ->where('TotalPoints', '>', 0)
                                    ->where('IfDelete', 0);
                    $SpendPoints = PTSTransProcessHistory::select('TotalPoints')
                                    ->where('PtsUserID', $this->UserID)
                                    ->where('TransStatus', 1)
                                    ->where('TransactionType', 0)
                                    ->where('TotalPoints', '<', 0)
                                    ->where('IfDelete', 0);
                    $PendingPoints = PTSTransProcessHistory::select('TotalPoints')
                                    ->where('PtsUserID', $this->UserID)
                                    ->where('TransStatus', 1)
                                    ->where('TransactionType', 1)
                                    ->where('IfDelete', 0);

                    $AwardedPoints = $AwardedPoints->sum('TotalPoints');
                    if (is_nan($AwardedPoints) || $AwardedPoints == null) {
                        $AwardedPoints = 0;
                    }
                    $RemainingPoints = $RemainingPoints->sum('TotalPoints');
                    if (is_nan($RemainingPoints) || $RemainingPoints == null) {
                        $RemainingPoints = 0;
                    }
                    $SpendPoints = $SpendPoints->sum('TotalPoints');
                    if (is_nan($SpendPoints) || $SpendPoints == null) {
                        $SpendPoints = 0;
                    }
                    $PendingPoints = $PendingPoints->sum('TotalPoints');
                    if (is_nan($PendingPoints) || $PendingPoints == null) {
                        $PendingPoints = 0;
                    }
                    $this->responseBody['RemainingPoints'] = $RemainingPoints;
                    $this->responseBody['AwardedPoints'] = $AwardedPoints;
                    $this->responseBody['SpendPoints'] = $SpendPoints;
                    $this->responseBody['PendingPoints'] = $PendingPoints;
                    break;
            }
            if ($PointType != 'total') {
                $SubTotalPoints = $SubTotalPoints->sum('TotalPoints');
                if (is_nan($SubTotalPoints)) {
                    $SubTotalPoints = 0;
                }
                $this->responseBody['TotalPoints'] = $SubTotalPoints;
            }
            
        }

        return $this->responseBody;
    }

    // PointType: total=全部, awarded=獲得, spend=花費, pending=處理中
    public function PointsHistory($PointType='')
    {
        //get points history
        if ($this->responseBody['status'] == 0) {
            $PointsHistory = PTSTransProcessHistory::select('ProcessID', 'TransactionDescription', 'TransDate', 'TotalPoints', 'TransactionType')->where('PtsUserID', $this->UserID)->where('IfDelete', 0)->orderBy('TransDate', 'desc');
            switch ($PointType) {
                case 'awarded':
                    $PointsHistory = $PointsHistory->where('TransactionType', 0)->where('TotalPoints', '>', 0);
                    break;
                case 'spend':
                    $PointsHistory = $PointsHistory->where('TransactionType', 0)->where('TotalPoints', '<', 0);
                    break;
                case 'pending':
                    $PointsHistory = $PointsHistory->where('TransactionType', 1);
                    break;
                case 'total':
                    break;

                default :
                    $this->responseBody['status'] = 1;
                    $this->responseBody['message'] = "PointType error";
                    $this->responseBody['ErrorID'] = 3;
                    break;
            }
            if ($this->responseBody['status'] == 0) {
                if ($PointsHistory->count() != 0) {
                    $this->responseBody['data'] = $PointsHistory->get()->toArray();
                } else {
                    $this->responseBody['data'] = [];
                }
            }
        }

        return $this->responseBody;
    }

    public function getSourceExchangeRate()
    {
        return $this->SourceExchangeRate;
    }

    static function IsCheckFrequency($UserID, $TagID, $FrequencyID, $ProcessNum)
    {
        return 1;
    }

    static function IsCheckQuota($TagID, $ProcessNum)
    {
        return 1;
    }
}
