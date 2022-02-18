<?php

namespace Zoumi\MiniGame\listeners\events;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\Player;
use Zoumi\MiniGame\api\game\GameManager;
use Zoumi\MiniGame\api\WorldManager;
use Zoumi\MiniGame\entities\VillagerBW;
use Zoumi\MiniGame\Main;
use Zoumi\MiniGame\utils\Functions;

class WorldEvent implements Listener {

    public static $combatLogger = [];

    public function onPlace(BlockPlaceEvent $event){
        $player = $event->getPlayer();
        $level  = $event->getBlock()->getLevel()->getFolderName();
        if (isset(WorldManager::$world[$level])){
            if (!WorldManager::getPlace($level) && Main::getInstance()->playerCache[$player->getName()]["gamemode"] !== "1"){
                if (!$event->isCancelled()) $event->setCancelled(true);
            }
        }
    }

    public function onBreak(BlockBreakEvent $event){
        $player = $event->getPlayer();
        $level  = $event->getBlock()->getLevel()->getFolderName();
        if (isset(WorldManager::$world[$level])){
            if (!WorldManager::getBreak($level) && Main::getInstance()->playerCache[$player->getName()]["gamemode"] !== "1"){
                if (!$event->isCancelled()) $event->setCancelled(true);
            }
        }
    }

    public function onInteract(PlayerInteractEvent $event){
        $player = $event->getPlayer();
        $level = $player->getLevel()->getFolderName();
        if (isset(WorldManager::$world[$level])){
            if (!WorldManager::getInteract($level) && Main::getInstance()->playerCache[$player->getName()]["gamemode"] !== "1"){
                if (!$event->isCancelled()) $event->setCancelled(true);
            }
        }
    }

    public function onEntityDamage(EntityDamageEvent $event){
        $entity = $event->getEntity();
        $cause = $event->getCause();
        $level = $entity->getLevel()->getFolderName();
        if (isset(WorldManager::$world[$level])) {
            switch ($cause) {
                case EntityDamageEvent::CAUSE_FALL:
                    if (!WorldManager::getFall($level)){
                        if (!$event->isCancelled()) $event->setCancelled(true);
                    }
                    break;
                case EntityDamageEvent::CAUSE_SUFFOCATION:
                    if ($entity instanceof Player) {
                        if (!GameManager::isInGame($entity)) {
                            Functions::goToSpawn($entity);
                        }
                    }
                    break;
            }
        }
    }

    public function onEntityDamageByEntity(EntityDamageByEntityEvent $event){
        $entity = $event->getEntity();
        $damager = $event->getDamager();
        $level = $damager->getLevel()->getFolderName();
        if (isset(WorldManager::$world[$level])){
            if (!WorldManager::getDamageByEntity($level)){
                if (!$event->isCancelled()) $event->setCancelled(true);
            }
            if ($damager instanceof Player && $entity instanceof Player) {
                if (WorldManager::getAnti2v1($level)) {
                    if (isset(self::$combatLogger[$damager->getName()])){
                        if (self::$combatLogger[$damager->getName()]["target"] !== $entity->getName()){
                            if (!$event->isCancelled()) return $event->setCancelled(true);
                        }
                    }
                    if (isset(self::$combatLogger[$entity->getName()])){
                        if (self::$combatLogger[$entity->getName()]["target"] !== $damager->getName()){
                            if (!$event->isCancelled()) return $event->setCancelled(true);
                        }
                    }
                    self::$combatLogger[$damager->getName()] = [
                        "left" => time() + 5,
                        "target" => $entity->getName()
                    ];
                    self::$combatLogger[$entity->getName()] = [
                        "left" => time() + 5,
                        "target" => $damager->getName()
                    ];
                }
            }
        }
        if ($entity instanceof VillagerBW){
            if (!$event->isCancelled()) return $event->setCancelled(true);
        }
    }

    public function onDropItem(PlayerDropItemEvent $event){
        $player = $event->getPlayer();
        $level = $player->getLevel()->getFolderName();
        if (isset(WorldManager::$world[$level])){
            if (!WorldManager::getDropItem($level)){
                if (!$event->isCancelled()) $event->setCancelled(true);
            }
        }
    }

    public function onExhaust(PlayerExhaustEvent $event){
        if (!$event->isCancelled()) $event->setCancelled(true);
    }

}