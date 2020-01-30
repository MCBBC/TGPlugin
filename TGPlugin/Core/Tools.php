<?php

namespace App\TGPlugin\Core;

use App\Models\User;
use App\Models\Node;
use App\Models\Relay;
use App\Services\Config;
use DateTime;

class Tools
{

    /**
     * 根据流量值自动转换单位输出
     */
    public static function flowAutoShow($value = 0)
    {
        $kb = 1024;
        $mb = 1048576;
        $gb = 1073741824;
        $tb = $gb * 1024;
        $pb = $tb * 1024;
        if (abs($value) >= $pb) {
            return round($value / $pb, 2) . 'PB';
        }

        if (abs($value) >= $tb) {
            return round($value / $tb, 2) . 'TB';
        }

        if (abs($value) >= $gb) {
            return round($value / $gb, 2) . 'GB';
        }

        if (abs($value) >= $mb) {
            return round($value / $mb, 2) . 'MB';
        }

        if (abs($value) >= $kb) {
            return round($value / $kb, 2) . 'KB';
        }

        return round($value, 2) . 'B';
    }

    //虽然名字是toMB，但是实际上功能是from MB to B
    public static function toMB($traffic)
    {
        $mb = 1048576;
        return $traffic * $mb;
    }

    //虽然名字是toGB，但是实际上功能是from GB to B
    public static function toGB($traffic)
    {
        $gb = 1048576 * 1024;
        return $traffic * $gb;
    }


    /**
     * @param $traffic
     * @return float
     */
    public static function flowToGB($traffic)
    {
        $gb = 1048576 * 1024;
        return $traffic / $gb;
    }

    /**
     * @param $traffic
     * @return float
     */
    public static function flowToMB($traffic)
    {
        $gb = 1048576;
        return $traffic / $gb;
    }

    //获取随机字符串

    public static function genRandomNum($length = 8)
    {
        // 来自Miku的 6位随机数 注册验证码 生成方案
        $chars = '0123456789';
        $char = '';
        for ($i = 0; $i < $length; $i++) {
            $char .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $char;
    }

    public static function genRandomChar($length = 8)
    {
        // 密码字符集，可任意添加你需要的字符
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $char = '';
        for ($i = 0; $i < $length; $i++) {
            $char .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $char;
    }

    public static function genToken()
    {
        return self::genRandomChar(64);
    }

    public static function is_ip($a)
    {
        return preg_match("/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/", $a);
    }


    // Unix time to Date Time
    public static function toDateTime($time)
    {
        return date('Y-m-d H:i:s', $time);
    }

    public static function secondsToTime($seconds)
    {
        $dtF = new DateTime('@0');
        $dtT = new DateTime("@$seconds");
        return $dtF->diff($dtT)->format('%a 天, %h 小时, %i 分 + %s 秒');
    }

    public static function genSID()
    {
        $unid = uniqid(Config::get('key'), true);
        return Hash::sha256WithSalt($unid);
    }

    public static function genUUID()
    {
        // @TODO
        return self::genSID();
    }

    public static function getLastPort()
    {
        $user = User::orderBy('id', 'desc')->first();
        if ($user == null) {
            return 1024; // @todo
        }
        return $user->port;
    }

    public static function getAvPort()
    {
        //检索User数据表现有port
        $det = User::pluck('port')->toArray();
        $port = array_diff(range(Config::get('min_port'), Config::get('max_port')), $det);
        shuffle($port);
        return $port[0];
    }

    public static function base64_url_encode($input)
    {
        return strtr(base64_encode($input), array('+' => '-', '/' => '_', '=' => ''));
    }

    public static function base64_url_decode($input)
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }
    
    public static function JsonToArray($input)
    {
        return json_decode($input, JSON_UNESCAPED_UNICODE);
    }
    
    public static function ArrayToJson($input)
    {
        return json_encode($input, JSON_UNESCAPED_UNICODE);
    }
    
    public static function Json($code, $msg = '', $data = array())
    {
        $result = array(
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        );
        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }
}
