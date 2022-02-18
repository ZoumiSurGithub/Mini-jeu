<?php

namespace Zoumi\MiniGame\tasks;

use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use Zoumi\MiniGame\Main;

class XYZTask extends Task {

    public function onRun(int $currentTick)
    {
        if (empty(Main::getInstance()->xyz)) return false;
        foreach (Main::getInstance()->xyz as $player){
            $p = Server::getInstance()->getPlayerExact($player);
            if ($p instanceof Player){
                $p->sendPopup("X:" . $p->getX() . " Y:" . $p->getY() . " Z:" . $p->getZ());
            }else{
                unset(Main::getInstance()->xyz[$player]);
            }
        }
    }

}