<?php


namespace App\HttpController\Backstage;


use App\Helper\Backstage\BackstageErrorCode;
use App\Helper\OrderHelper;
use App\Helper\TimeHelper;
use App\Models\Backstage\AdminPlayer;
use App\Models\Backstage\AdminSendTokenHistory;
use App\Models\UserRechargeOrder;
use EasySwoole\EasySwoole\Task\TaskManager;

class Player extends AdminBase
{
    public function list()
    {
        $param = $this->json();
        $pageIndex = $param['page']??1;
        $pageSize = $param['limit']??10;
        $uid = $param['userId']??'';
        $search = $param['search']??'';
        $data = AdminPlayer::create()->getPlayList($uid,$search,$pageIndex,$pageSize);
        $this->writeJsonSuccess($data);
    }


    public function modifyToken()
    {
        $param = $this->json();
        $uid = $param['uid']??'';
        if(empty($uid))
        {
            $this->writeJsonError(BackstageErrorCode::SendTokenError,'发送失败');
            return;
        }
        $isAddFlow = $param['isAddFlow']??0;
        $remark = $param['remark']??'';
        $balance = $param['balance']??0;
        $freeze_balance = $param['freeze_balance']??0;
        $handsel_balance = $param['handsel_balance']??0;
        $changeBalance = $balance;
        if($handsel_balance>=0){
            $changeBalance += $handsel_balance;
        }else{
            $changeBalance -= abs($handsel_balance);
        }
        if(!empty($changeBalance))
        {
            $changeBalance = round($changeBalance,2)*100;
        }
        if(!empty($freeze_balance))
        {
            $freeze_balance = round($freeze_balance,2)*100;
        }
        if(!empty($handsel_balance))
        {
            $handsel_balance = round($handsel_balance,2)*100;
        }
        $order_id = OrderHelper::genOrderId('PGB_');
        $data = [
            'order_id'=>$order_id,
            'uid'=>$uid,
            'recharge_value'=>$changeBalance,
            'give_away_value'=>$handsel_balance,
            'source'=>1,
            'activity_id'=>0,
            'create_time'=>TimeHelper::getNowTime(),
            'modify_time'=>TimeHelper::getNowTime(),
            'status'=>1,
            'pay_channel'=>0,
            'admin_id'=>$this->admin_token_uid,
            'remark'=>$remark
        ];
        $recharge_value = $changeBalance;
        $flowData = [
            'uid'=>$uid,
            'order_id'=>$order_id,
            'recharge_value'=>$recharge_value,
            'complete_value'=>0,
            'create_time'=>TimeHelper::getNowTime(),
            'modify_time'=>TimeHelper::getNowTime(),
            'status'=>0,
            'source'=>1
        ];
        if(!empty($isAddFlow))
        {
            //添加流水任务
            $resultOrder = UserRechargeOrder::create()->addRechargeOrderForTrans($data,$flowData);

            if(!$resultOrder)
            {
                $this->writeJsonError(BackstageErrorCode::SendTokenError,'后台充值订单创建失败');
                return;
            }
            $handsel_balance = 0;//赠送流水冻结已经在任务中添加
        }else {

            $resultOrder = UserRechargeOrder::create()->addRechargeOrder($data);

            if(!$resultOrder)
            {
                $this->writeJsonError(BackstageErrorCode::SendTokenError,'后台充值订单创建失败');
                return;
            }
        }


        $result = AdminPlayer::create()->modifyPlayerBalance($uid,$changeBalance,$freeze_balance,$handsel_balance,'admin',$remark,$this->admin_token_uid);

        if(!$result)
        {
            $this->writeJsonError(BackstageErrorCode::SendTokenError,'发送失败');
            return;
        }
        $data = [
            'uid'=>$uid,
            'admin_id'=>$this->admin_token_uid,
            'balance'=>round($balance,2)*100,
            'freeze_balance'=>$freeze_balance,
            'handsel_balance'=>$handsel_balance,
            'remark'=>$remark
        ];
        TaskManager::getInstance()->async(function () use ($data){
            AdminSendTokenHistory::create()->saveLogs($data);
        });
        $this->writeJsonSuccess();
    }


    public function resetPlayerPassword()
    {
        $param = $this->json();
        $uid = $param['uid']??'';
        $password = $param['password']??'';
        if(empty($uid)||empty($password))
        {
            $this->writeJsonError(BackstageErrorCode::ParamError,'参数错误');
            return;
        }
        $result = AdminPlayer::create()->updatePlayerPassword($uid,$password);
        if(!$result)
        {
            $this->writeJsonError(BackstageErrorCode::ModifyError,'修改失败');
            return;
        }
        $this->writeJsonSuccess();
    }


    public function tokenHistory()
    {
        $param = $this->json();
        $pageIndex = $param['page']??1;
        $pageSize = $param['limit']??10;
        $uid = $param['userId']??'';
        $search = $param['search']??'';
        $data = AdminSendTokenHistory::create()->list($uid,$search,$pageIndex,$pageSize);
        $this->writeJsonSuccess($data);
    }

    public function changeUserAgentStatus()
    {
        $param = $this->json();
        $uid = $param['uid']??'';
        $is_agent = $param['is_agent']??'0';
        if(empty($uid))
        {
            $this->writeJsonError(BackstageErrorCode::ParamError,'参数错误');
            return;
        }
        $result = AdminPlayer::create()->changeUserAgentStatus($uid,$is_agent);
        if(!$result)
        {
            $this->writeJsonError(BackstageErrorCode::ModifyError,'修改失败');
            return;
        }
        $this->writeJsonSuccess();
    }
}