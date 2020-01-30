<?php

namespace App\TGPlugin;

use App\TGPlugin\HongBao\HBPlugin;

class TGPlugin
{
    /**
     * 红包操作
     */
     
    //创建红包
    public static function Create_HB($TelegramID, $TGName, $Flow = NULL)
    {
        return HBPlugin::Create($TelegramID, $TGName, $Flow);
    }
    
    //领取红包
    public static function Draw_HB($TelegramID, $Token, $TGName)
    {
        return HBPlugin::Draw($TelegramID, $Token, $TGName);
    }
    
    //查询失效红包
    public static function Query_HB()
    {
        return HBPlugin::Query();
    }
}