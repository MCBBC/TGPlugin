<?php
//路由
$app->get('/bigger/{name}/{data}/{token}', App\TGPlugin\Core\Bigger::class . ':Create_Bigger_Image');

//机器人修改一
use App\TGPlugin\TGPlugin;

//机器人修改二
case (substr($callback_data, 0, 4) == 'draw'):
    $token = substr($callback_data, 5);
    if($callback->getFrom()->getUsername()){
        $TGName = $callback->getFrom()->getUsername();
    } else {
        $TGName = $callback->getFrom()->getFirstname() . $callback->getFrom()->getLastname();
    }
    $draw = TGPlugin::Draw_HB($user->telegram_id, $token, $TGName);
    $draw = json_decode($draw);
    $reply_message = $draw->msg;
    break;

} else {
    $reply_message = '您未绑定本站账号，您可以进入网站的“资料编辑”，在右下方绑定您的账号';
}

//机器人修改三
case '流量红包':
    $reply['message'] = '请在群组内使用此命令';
    break;
    
//机器人修改四
case 'help':
    $reply['message'] = '命令列表：
		/ping  获取群组ID
		/traffic 查询流量
		/checkin 签到
		/help 获取帮助信息
		/rss 获取节点订阅
		/流量红包 例如/流量红包 1024（单位为MB），默认1024MB';
    if ($user == null) {
        $reply['message'] .= PHP_EOL . '您未绑定本站账号，您可以进入网站的“资料编辑”，在右下方绑定您的账号';
    }
    break;

case '流量红包':
    if($user == null){
        $reply['message'] = '您未绑定本站账号，您可以进入网站的“资料编辑”，在右下方绑定您的账号';
    } else {
        //获取TG用户信息
        if($message->getFrom()->getUsername()){
            $TGName = $message->getFrom()->getUsername();
        } else {
            $TGName = $message->getFrom()->getFirstname() . ' ' . $message->getFrom()->getLastname();
        }
        $flow = mb_substr($message->getText(), 6);
        $create = TGPlugin::Create_HB($user->telegram_id, $TGName, $flow);
        $create = json_decode($create);
        if($create->code == '200'){
            $reply['message'] = $create->data->Img;
            $keys[] = [['text' => '领取红包', 'callback_data' => 'draw '.$create->data->token.'']];
            $reply['markup'] = new InlineKeyboardMarkup(
                $keys
            );
        } else {
            $reply['message'] = $create->msg;
        }
    }
    break;
    
//机器人修改五
if($command === '流量红包' and $create->code === '200'){
    $bot->sendPhoto($message->getChat()->getId(), $reply['message'], $parseMode = null, $disablePreview = false, $replyMarkup = $reply['markup']);
    $bot->sendChatAction($message->getChat()->getId(), '');
} else {
    $bot->sendMessage($message->getChat()->getId(), $reply['message'], $parseMode = null, $disablePreview = false, $replyToMessageId = $reply_to, $replyMarkup = $reply['markup']);
    $bot->sendChatAction($message->getChat()->getId(), '');
}

$command_list = array('ping', 'traffic', 'help', 'prpr', 'checkin', 'rss', '流量红包');