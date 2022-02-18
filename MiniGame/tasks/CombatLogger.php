<?php

namespace Zoumi\MiniGame\tasks;

use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use Zoumi\MiniGame\listeners\events\WorldEvent;
use Zoumi\MiniGame\Manager;

class CombatLogger extends Task {

    public function onRun(int $currentTick)
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $player){
            if ($player instanceof Player){
                if (isset(WorldEvent::$combatLogger[$player->getName()])){
                    $target = Server::getInstance()->getPlayerExact(WorldEvent::$combatLogger[$player->getName()]["target"]);
                    if ($target instanceof Player) {
                        if (time() >= WorldEvent::$combatLogger[$player->getName()]["left"]) {
                            $player->sendMessage(Manager::PREFIX_INFOS . "Vous n'êtes plus en combat.");
                            unset(WorldEvent::$combatLogger[$player->getName()]);
                        }
                    }else{
                        $player->sendMessage(Manager::PREFIX_INFOS . "Vous n'êtes plus en combat.");
                        unset(WorldEvent::$combatLogger[$player->getName()]);
                    }
                }
            }
        }
    }

}