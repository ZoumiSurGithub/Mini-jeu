<?php

namespace Zoumi\MiniGame\listeners;

use http\Client\Curl\User;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\Server;
use Zoumi\MiniGame\api\Cosmetique;
use Zoumi\MiniGame\api\game\GameManager;
use Zoumi\MiniGame\api\game\SPlayer;
use Zoumi\MiniGame\api\Money;
use Zoumi\MiniGame\api\Users;
use Zoumi\MiniGame\api\WorldManager;
use Zoumi\MiniGame\commands\staff\Entity;
use Zoumi\MiniGame\entities\FireworksRocket;
use Zoumi\MiniGame\entities\VillagerTest;
use Zoumi\MiniGame\items\Fireworks;
use Zoumi\MiniGame\listeners\events\WorldEvent;
use Zoumi\MiniGame\Main;
use Zoumi\MiniGame\Manager;
use Zoumi\MiniGame\utils\Functions;

class PlayerListener implements Listener {

    public function onPreLogin(PlayerPreLoginEvent $event){
        $player = $event->getPlayer();
        if (Server::getInstance()->hasWhitelist()){
            if (!$player->isWhitelisted()){
                $player->close("", "§l" . Manager::PREFIX . "Serveur en développement.\n§lRejoins notre serveur §bdiscord§f.\n§l§bdiscord.gg/wC2h4SHMaU");
                return;
            }
        }
        if (!Users::playerExist($player->getName())){
            var_dump("yes");
            Users::createUser($player);
            Main::getInstance()->playerCache[$player->getName()] = [
                "gamemode" => "0"
            ];
        }else{
            Main::getInstance()->playerCache[$player->getName()] = [
                "gamemode" => Users::getGamemode($player)
            ];
        }
    }

    public function onPlayerCreate(PlayerCreationEvent $event){
        $event->setPlayerClass(SPlayer::class);
    }

    public function onJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        $event->setJoinMessage("§7[§d+§7] §d" . $player->getName());
        Cosmetique::sendEffectJoin($player);
        Functions::goToSpawn($player);
        if (!isset(Functions::$villagerTest[$player->getName()])) {
            Functions::sendPnjTest($player);
        }
    }

    public function onQuit(PlayerQuitEvent $event){
        $player = $event->getPlayer();
        $event->setQuitMessage("§7[§5-§7] §5" . $player->getName());
        if ($player instanceof SPlayer){
            $player->setGame(null);
        }
        Cosmetique::sendEffectQuit($player);
    }

    public function onInteract(PlayerInteractEvent $event)
    {
        $action = $event->getAction();
        $player = $event->getPlayer();
        $item = $event->getItem();
        $block = $event->getBlock();
        if ($player instanceof SPlayer) {
            if ($action === PlayerInteractEvent::RIGHT_CLICK_AIR) {
                if ($item->getCustomName() === "§r§l§5MINI §dJEUX") {
                    FormListener::sendMiniGame($player);
                    return;
                }
                if ($item->getCustomName() === "§r§l§aJUMP") {
                    if (Functions::isInGame($player)) {
                        $player->sendMessage(Manager::PREFIX_ALERT . "Impossible de vous téléportez au jump car vous êtes dans une partie.");
                        return;
                    }
                    $player->teleport(new Position(-415.5, 61, -322.5, Server::getInstance()->getLevelByName("spawn")));
                    $player->sendMessage(Manager::PREFIX_INFOS . "Vous venez d'être téléporter au jump.");
                    return;
                }
                if ($item->getCustomName() === "§r§l§eCOSMETIQUE") {
                    Cosmetique::sendCosmeticMenu($player);
                    return;
                }
                if ($item->getCustomName() === "§r§l§cENTRER UN CODE") {
                    FormListener::sendEnterCodeMenu($player);
                    return;
                }
            }
            if ($block->getX() === -469 && $block->getY() === 65 && $block->getZ() === -374) {
                if (Main::getInstance()->entityCount === 0) {
                    if (!empty(Main::getInstance()->entityId)) {
                        if (Server::getInstance()->getDefaultLevel()->getEntity(Main::getInstance()->entityId) instanceof \pocketmine\entity\Entity) {
                            Server::getInstance()->getDefaultLevel()->getEntity(Main::getInstance()->entityId)->flagForDespawn();
                        }
                    }
                    $nbt = \pocketmine\entity\Entity::createBaseNBT(new Position(-470.5, 68, -373.5, Server::getInstance()->getLevelByName("spawn")), null, 0, 0);
                    $entity = \pocketmine\entity\Entity::createEntity("Kill", Server::getInstance()->getDefaultLevel(), $nbt);
                    $entity->spawnToAll();
                    Main::getInstance()->entityId = $entity->getId();
                    Main::getInstance()->entityCount = Main::getInstance()->entityCount + 1;
                    return;
                } elseif (Main::getInstance()->entityCount === 1) {
                    if (!empty(Main::getInstance()->entityId)) {
                        if (Server::getInstance()->getDefaultLevel()->getEntity(Main::getInstance()->entityId) instanceof \pocketmine\entity\Entity) {
                            Server::getInstance()->getDefaultLevel()->getEntity(Main::getInstance()->entityId)->flagForDespawn();
                        }
                    }
                    $nbt = \pocketmine\entity\Entity::createBaseNBT(new Position(-470.5, 68, -373.5, Server::getInstance()->getLevelByName("spawn")), null, 0, 0);
                    $entity = \pocketmine\entity\Entity::createEntity("Death", Server::getInstance()->getDefaultLevel(), $nbt);
                    $entity->spawnToAll();
                    Main::getInstance()->entityId = $entity->getId();
                    Main::getInstance()->entityCount = Main::getInstance()->entityCount + 1;
                    return;
                } elseif (Main::getInstance()->entityCount === 2) {
                    if (!empty(Main::getInstance()->entityId)) {
                        if (Server::getInstance()->getDefaultLevel()->getEntity(Main::getInstance()->entityId) instanceof \pocketmine\entity\Entity) {
                            Server::getInstance()->getDefaultLevel()->getEntity(Main::getInstance()->entityId)->flagForDespawn();
                        };
                    }
                    $nbt = \pocketmine\entity\Entity::createBaseNBT(new Position(-470.5, 68, -373.5, Server::getInstance()->getLevelByName("spawn")), null, 0, 0);
                    $entity = \pocketmine\entity\Entity::createEntity("Kd", Server::getInstance()->getDefaultLevel(), $nbt);
                    $entity->spawnToAll();
                    Main::getInstance()->entityId = $entity->getId();
                    Main::getInstance()->entityCount = Main::getInstance()->entityCount + 1;
                    return;
                } elseif (Main::getInstance()->entityCount === 3) {
                    if (!empty(Main::getInstance()->entityId)) {
                        if (Server::getInstance()->getDefaultLevel()->getEntity(Main::getInstance()->entityId) instanceof \pocketmine\entity\Entity) {
                            Server::getInstance()->getDefaultLevel()->getEntity(Main::getInstance()->entityId)->flagForDespawn();
                        }
                    }
                    $nbt = \pocketmine\entity\Entity::createBaseNBT(new Position(-470.5, 68, -373.5, Server::getInstance()->getLevelByName("spawn")), null, 0, 0);
                    $entity = \pocketmine\entity\Entity::createEntity("Money", Server::getInstance()->getDefaultLevel(), $nbt);
                    $entity->spawnToAll();
                    Main::getInstance()->entityId = $entity->getId();
                    Main::getInstance()->entityCount = Main::getInstance()->entityCount + 1;
                    return;
                } elseif (Main::getInstance()->entityCount === 4) {
                    if (!empty(Main::getInstance()->entityId)) {
                        if (Server::getInstance()->getDefaultLevel()->getEntity(Main::getInstance()->entityId) instanceof \pocketmine\entity\Entity) {
                            Server::getInstance()->getDefaultLevel()->getEntity(Main::getInstance()->entityId)->flagForDespawn();
                        }
                    }
                    Main::getInstance()->entityId = null;
                    Main::getInstance()->entityCount = 0;
                    return;
                }
            }
        }
    }

    public function onDeath(PlayerDeathEvent $event){
        $entity = $event->getEntity();
        $cause = $entity->getLastDamageCause();
        if ($cause instanceof EntityDamageByEntityEvent){
            $damager = $cause->getDamager();
            if ($damager instanceof SPlayer && $entity instanceof SPlayer){
                if (WorldManager::getAnti2v1($damager->getLevel()->getFolderName())){
                    if (isset(WorldEvent::$combatLogger[$damager->getName()])){
                        unset(WorldEvent::$combatLogger[$damager->getName()]);
                    }
                    if (isset(WorldEvent::$combatLogger[$entity->getName()])){
                        unset(WorldEvent::$combatLogger[$entity->getName()]);
                    }
                }
                Cosmetique::sendEffectKill($damager, $entity);
                Cosmetique::sendEffectDeath($entity, $damager);
                Users::addKill($damager);
                Users::addDeath($entity);
                Users::setKd($damager);
                Users::setKd($entity);
                Functions::addXp($damager, 5);
                $event->setDeathMessage(null);
            }
        }
    }

    public function onEntityDamage(EntityDamageEvent $event){
        $entity = $event->getEntity();
        $cause = $event->getCause();
        if ($entity instanceof Player) {
            switch ($cause) {
                case EntityDamageEvent::CAUSE_VOID:
                    if (!Functions::isInGame($entity)) {
                        if (!$event->isCancelled()) $event->setCancelled(true);
                        $entity->teleport(Server::getInstance()->getDefaultLevel()->getSafeSpawn());
                    }
                    break;
            }
        }
    }

    public function onEntityDamageByEntity(EntityDamageByEntityEvent $event){
        $entity = $event->getEntity();
        $damager = $event->getDamager();
        if ($damager instanceof Player && $entity instanceof VillagerTest){
            $entity->sendEffectKill($damager, $entity);
        }
    }

}