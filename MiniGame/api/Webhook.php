<?php

namespace Zoumi\MiniGame\api;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;

class Webhook {

    public static function sendGameLogs(string $message){
        $webhook = new \CortexPE\DiscordWebhookAPI\Webhook("https://discord.com/api/webhooks/854513065927639082/jtTkj_ZvnHW0H6_Xr43OxPBh5fWxhnxNx2T3t3hH2uuChNLNqpsXqQMPd0pYpWlEJ6J1");
        $msg = new Message();
        $embed = new Embed();
        $embed->setTitle("SunParadise - Logs");
        $embed->setDescription($message);
        $embed->setFooter("SunParadise");
        $embed->setTimestamp(new \DateTime('now'));
        $msg->addEmbed($embed);
        $webhook->send($msg);
    }

    public static function sendStats($message){
        $webhook = new \CortexPE\DiscordWebhookAPI\Webhook("https://discord.com/api/webhooks/854516055018569759/pHae8KPLEZvksIZKrfzfYe-4iwcWIZJv9m0T461N3B6xffyZkuOvqF7-bsayp6IYrCZh");
        $msg = new Message();
        $embed = new Embed();
        $embed->setTitle("SunParadise - Statistiques du serveur");
        $embed->setDescription($message);
        $embed->setTimestamp(new \DateTime('now'));
        $embed->setFooter("SunParadise");
        $msg->addEmbed($embed);
        $webhook->send($msg);
    }

}