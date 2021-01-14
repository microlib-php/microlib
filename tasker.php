<?php

use app\Tools;
use lib\Api;

include "./bot.php";
$mysql->query("use test");
$bots = $mysql->query("select * from api_tools")->fetch_all(1);

foreach ($bots as $_bot) {
    $api = new Api($_bot['token']);
    $info = Tools::parse_info($api->get_webhook_info()->result);
    if (isset($info['last_error_date'])) {
        if ($info['pending_updates'] > $_bot['pending_max']) {
            $bot->send_message($_bot['user_id'], $_bot['username'] . "da qandaydur muammo borga o'xshaydi. WebhookInfo: \n" . Tools::generate_webhook_info($api));
            continue;
        }
        if ($_bot['last_error_date'] == $info['last_error_date']) continue;
        $mysql->query("update api_tools set last_error_date = '{$info['last_error_date']}' where id = {$_bot['id']}");
        $bot->send_message($_bot['user_id'], $_bot['username'] . "da qandaydur muammo borga o'xshaydi. WebhookInfo: \n" . Tools::generate_webhook_info($api));
    }
}