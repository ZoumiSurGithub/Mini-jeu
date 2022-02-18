<?php

namespace Zoumi\MiniGame\tasks;

use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use Zoumi\MiniGame\api\Webhook;

class StatsServer extends Task {

    public function onRun(int $currentTick)
    {
        $players = [];
        foreach (Server::getInstance()->getOnlinePlayers() as $player){
            if ($player instanceof Player){
                $players[] = $player->getName();
            }
        }
        Webhook::sendStats(
            "> TPS: " . Server::getInstance()->getTickUsage() . "\n" .
            "> TPS average: " . Server::getInstance()->getTickUsageAverage() . "\n" .
            "> Players (" . count(Server::getInstance()->getOnlinePlayers()) . "/" . Server::getInstance()->getMaxPlayers() . "):\n" .
            (implode(", ", $players) ?? "Aucun")
        );
    }

}