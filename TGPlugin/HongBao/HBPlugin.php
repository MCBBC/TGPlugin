<?php

namespace App\TGPlugin\HongBao;

use App\Models\User;
use App\Models\Model;
use App\Services\Config;
use App\TGPlugin\Core\Tools;

class TGPluginDB extends Model
{
    protected $connection = 'default';
    protected $table = 'user_hongbao';
}

class HBPlugin
{
    //创建红包
    public static function Create($TelegramID, $TGName, $Flow = NULL)
    {
        //用户最少有多少流量 单位B 默认值为10GB
        $MinFlow = '10737418240';
        
        //获取用户信息
        $User = User::where('telegram_id', $TelegramID)->first();
        
        if(empty($Flow) && $Flow == NULL) {
            //判断$Flow是否为空 单位GB 默认值为1GB
            $Flow = '1';
        }
        
        if(!is_numeric($Flow)) {
            //判断$Flow是否为纯数字
            return Tools::Json('201', '@'.$TGName.' 流量必须为纯数字');
        }
        
        if($User->transfer_enable <= $MinFlow) {
            //判断用户剩余流量是否低于默认阈值
            return Tools::Json('201', '@'.$TGName.' 无法创建流量红包，剩余流量不足' . Tools::flowAutoShow($MinFlow));
        }
        
        if(($User->transfer_enable - Tools::toGB($Flow)) < $MinFlow) {
            //判断创建红包后用户剩余流量是否低于默认阈值
            return Tools::Json('201', '@'.$TGName.' 无法创建流量红包，创建红包后剩余流量不足' . Tools::flowAutoShow($MinFlow));
        }
        
        //单位转换为B
        $Flow = Tools::toGB($Flow);
        
        //获取随机字符串作为Token
        $Token = Tools::genRandomChar('10');
        
        //设置过期时间为当前时间+1天
        $Time = strtotime(date('Y-m-d H:i:s', strtotime('+1 day')));
        
        //扣除用户流量
        $User->transfer_enable = $User->transfer_enable - $Flow;
        $User->save();
        
        //插入数据库
        $Insert = new TGPluginDB();
        $Insert->telegram_id = $TelegramID;
        $Insert->max_quantity = 0;
        $Insert->min_quantity = 0;
        $Insert->max_flow = $Flow;
        $Insert->min_flow = $Flow;
        $Insert->nowTime = $Time;
        $Insert->token = $Token;
        $Insert->state = 1;
        $Insert->draw = '[]';
        $Insert->save();
        
        //保存内容至数组
        $Flow = Tools::flowAutoShow($Flow);
        $Data = array('token' => $Token, 'flow' => $Flow, 'TGName' => $TGName, 'Img' => 'https://test.aoaomoe.me/bigger/' . $TGName . '/' . $Flow . '/' . $Token . '?ver=' . time());
        
        //输出
        return Tools::Json('200', '流量红包创建成功', $Data);
    }
    
    //领取红包
    public static function Draw($TelegramID, $Token, $TGName)
    {
        //当前时间
        $CurrentTimes = strtotime(date("Y-m-d H:i:s"));
        
        //最后一位用户保底流量 单位B 默认值为100MB
        $MinFlowDraw = '104857600';
        
        //获取用户信息
        $User = User::where('telegram_id', $TelegramID)->first();
        
        //获取红包信息
        $HongBao = TGPluginDB::where('token', $Token)->first();
        $Arr = json_decode($HongBao->draw);
        
        if($HongBao->state != '1'){
            //判断红包是否有效
            return Tools::Json('201', '@'.$TGName.' 红包已失效');
        }
        
        if($HongBao->nowTime < $CurrentTimes) {
            //判断红包是否过期
            $User->transfer_enable = $User->transfer_enable + $HongBao->min_flow; //当前流量 + 剩余流量
            $User->save();
            $HongBao->state = 0;
            $HongBao->save();
            return Tools::Json('201', '@'.$TGName.' 红包已过期');
        }
        
        if($HongBao->min_flow <= '0') {
            //判断红包流量是否为空
            $HongBao->state = 0;
            $HongBao->save();
            return Tools::Json('201', '@'.$TGName.' 红包已领完');
        }
        
        if(in_array($TelegramID, $Arr)) {
            //判断用户是否领取过这个红包
            return Tools::Json('201', '@'.$TGName.' 您已领取过这个红包');
        }
        
        if($HongBao->min_flow <= $MinFlowDraw) {
            //剩余流量低于保底流量，剩余流量都由最后用户获得
            $User->transfer_enable = $User->transfer_enable + $HongBao->min_flow; //当前流量 + 剩余流量
            $User->save();
            $HongBao->state = 0;
            $HongBao->save();
            return Tools::Json('200', '@'.$TGName.' 恭喜您获得 '. Tools::flowAutoShow($HongBao->min_flow) .' 流量');
        }
        
        //随机数值并更新数据库
        $Flow_rand = floor(mt_rand($MinFlowDraw, $HongBao->min_flow) / 3); //单位B
        $User->transfer_enable = $User->transfer_enable + $Flow_rand; //当前流量 + 获得流量
        $User->save();
        //插入数组
        array_push($Arr, $TelegramID);
        $HongBao->draw = json_encode($Arr);
        $HongBao->min_flow = $HongBao->min_flow - $Flow_rand; //当前流量 - 获得流量
        $HongBao->save();
        
        //保存内容至数组
        $Data = array("flow" => Tools::flowAutoShow($HongBao->min_flow));
        
        return Tools::Json('200', '@'.$TGName.' 恭喜您获得 '. Tools::flowAutoShow($Flow_rand) .' 流量，红包剩余流量：' . Tools::flowAutoShow($HongBao->min_flow), $Data);
    }
    
    //查询失效红包
    public static function Query()
    {
        //当前时间
        $CurrentTimes = strtotime(date("Y-m-d H:i:s"));
        
        //获取红包信息
        $HongBao_Enable = TGPluginDB::where('state', '1')->get();
        
        //设置变量
        $DataInt = 0;
        
        foreach ($HongBao_Enable as $val) {
            //获取用户信息
            $User = User::where('telegram_id', $val['telegram_id'])->first();
            //获取红包信息
            $HongBao = TGPluginDB::where('token', $val['token'])->first();
            if($val['nowTime'] < $CurrentTimes) {
                $DataInt = $DataInt + 1;
                //判断红包是否过期
                $User->transfer_enable = $User->transfer_enable + $val['min_flow']; //当前流量 + 剩余流量
                $User->save();
                $HongBao->min_flow = 0;
                $HongBao->state = 0;
                $HongBao->save();
            }
        }
        
        $Data = array("DataInt" => $DataInt);
        
        return Tools::Json('200', '流量红包已刷新', $Data);
    }
}