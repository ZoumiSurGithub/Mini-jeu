<?php

namespace Zoumi\MiniGame\listeners\events;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\Player;
use Zoumi\MiniGame\listeners\FormListener;

class WaitingRoomEvent implements Listener {

    public function onInteract(PlayerInteractEvent $event){
        $player = $event->getPlayer();
        $item = $event->getItem();
        $action = $event->getAction();
        if ($item->getCustomName() === "§l§fALEATOIRE" && $action === PlayerInteractEvent::RIGHT_CLICK_AIR){
            FormListener::sendSelectTeam($player);
        }elseif ($item->getCustomName() === "§l§cROUGE" && $action === PlayerInteractEvent::RIGHT_CLICK_AIR){
            FormListener::sendSelectTeam($player);
        }elseif ($item->getCustomName() === "§l§1BLEU" && $action === PlayerInteractEvent::RIGHT_CLICK_AIR){
            FormListener::sendSelectTeam($player);
        }elseif ($item->getCustomName() === "§l§eJAUNE" && $action === PlayerInteractEvent::RIGHT_CLICK_AIR){
            FormListener::sendSelectTeam($player);
        }elseif ($item->getCustomName() === "§l§2VERT" && $action === PlayerInteractEvent::RIGHT_CLICK_AIR){
            FormListener::sendSelectTeam($player);
        }
    }

}