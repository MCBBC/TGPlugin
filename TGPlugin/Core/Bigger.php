<?php

namespace App\TGPlugin\Core;

class Bigger
{
    public static function Create_Bigger_Image($request, $response, $args) 
    {
        $im = imagecreatetruecolor(242, 400); //创建画布大小 272x400
        $white = imagecolorallocate($im, 235, 205, 153); //设置颜色变量
        $english_font = BASE_PATH . '/app/TGPlugin/Core/Assets/Montserrat-Regular.ttf'; //引入字体文件
        $chinese_font = BASE_PATH . '/app/TGPlugin/Core/Assets/hanyixizhongyuan.ttf'; //引入字体文件
        imagefill($im, 0, 0, $white); //填充画布背景
        /** 背景 **/
        $head_img = imagecreatefromstring(file_get_contents(BASE_PATH . '/app/TGPlugin/Core/Assets/bg.png')); //获取图片
        imagecopy($im, $head_img, 0, 0, 0, 0, 242, 400); //将图片放入画布
        /** 背景 **/
        /** 用户名 **/
        $fontBox_name = imagettfbbox(12, 0, $english_font, '@'.$args['name']);
        imagettftext($im, 12, 0, ceil((242 - $fontBox_name[2]) / 2), 110, $white, $english_font, '@'.$args['name']); //生成文字
        /** 用户名 **/
        /** 流量 **/
        $fontBox_data = imagettfbbox(17, 0, $english_font, $args['data']);
        imagettftext($im, 17, 0, ceil((242 - $fontBox_data[2]) / 2), 165, $white, $english_font, $args['data']); //生成文字
        /** 流量 **/
        /** Token **/
        $fontBox_token = imagettfbbox(8, 0, $english_font, 'ID: '.$args['token']);
        imagettftext($im, 8, 0, ceil((242 - $fontBox_token[2]) / 2), 373, $white, $english_font, 'ID: '.$args['token']); //生成文字
        /** Token **/
        imagepng($im);
        $Response = $response->withHeader('Content-type', ' image/png');
        return $Response;
    }
}