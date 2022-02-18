<?php

namespace Zoumi\MiniGame\listeners\events;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\Server;
use Zoumi\MiniGame\api\game\GunPlay;
use Zoumi\MiniGame\Manager;
use Zoumi\MiniGame\utils\Functions;

class GunPlayEvent implements Listener {

    public function onRespawn(PlayerRespawnEvent $event){
        $player = $event->getPlayer();
        if (Functions::isGameIs($player, "GunPlay")){
            $gunplay = Functions::getGameByHoster(Functions::getHoster($player));
            $rand = mt_rand(0, 11);
            $pos = explode(":", Manager::POS_GUNPLAY[$rand]);
            $event->setRespawnPosition(new Position($pos[0], $pos[1], $pos[2], Server::getInstance()->getLevelByName($gunplay->getId())));
            $gunplay->sendWeapon($player, $gunplay->getPoint($player));
        }
    }

    public function onDead(PlayerDeathEvent $event){
        $entity = $event->getEntity();
        $cause = $entity->getLastDamageCause();
        $event->setDrops([]);
        $event->setDeathMessage(null);
        if ($cause instanceof EntityDamageByEntityEvent){
            $damager = $cause->getDamager();
            if ($entity instanceof Player && $damager instanceof Player) {
                if (Functions::isGameIs($entity, "GunPlay") && Functions::isGameIs($damager, "GunPlay")) {
                    $gunplay = Functions::getGameByHoster(Functions::getHoster($damager));
                    $gunplay->addPointOfPlayer($damager);
                    $gunplay->sendWeapon($damager, $gunplay->getPointOfPlayer($damager));
                    $gunplay->broadcastMessage("§5» §d" . $damager->getName() . " §fvient de tué §d" . $entity->getName() . " §7[" . $damager->getHealth() . "/" . $damager->getMaxHealth() . "]");
                    $damager->setHealth($damager->getMaxHealth());
                }
            }
        }
    }

    public function onQuit(PlayerQuitEvent $event){
        $player = $event->getPlayer();
        if (Functions::isGameIs($player, "GunPlay")){
            Functions::getGameByHoster(Functions::getHoster($player))->removeCurrentPlayer($player);
        }
    }

    public function onExhaust(PlayerExhaustEvent $event){
        $player = $event->getPlayer();
        if ($player instanceof Player) {
            if (Functions::isGameIs($player, "GunPlay")) {
                $player->setFood(16);
                $event->setCancelled(true);
            }
        }
    }

}