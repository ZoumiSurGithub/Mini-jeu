<?php

namespace Zoumi\MiniGame\listeners\events;

use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\item\Item;
use pocketmine\item\ItemBlock;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\Server;
use Zoumi\MiniGame\api\game\Hikabrain;
use Zoumi\MiniGame\api\game\HikabrainInstance;
use Zoumi\MiniGame\api\NavigatorManager;
use Zoumi\MiniGame\api\game\TeamManager;
use Zoumi\MiniGame\entities\FireworksRocket;
use Zoumi\MiniGame\items\Fireworks;
use Zoumi\MiniGame\Main;
use Zoumi\MiniGame\Manager;
use Zoumi\MiniGame\tasks\hikabrain\RoundTask;
use Zoumi\MiniGame\utils\Functions;

class HikabrainEvent implements Listener
{

    public static $blocks = [];

    public function onQuit(PlayerQuitEvent $event)
    {
        $player = $event->getPlayer();
        if (Functions::isGameIs($player, "Hikabrain")) {
            Hikabrain::removePlayer(Hikabrain::getId($player), $player);
        }
    }

    public function onEntityDamageEvent(EntityDamageEvent $event)
    {
        $entity = $event->getEntity();
        $cause = $event->getCause();
        if ($entity instanceof Player) {
            switch ($cause) {
                case EntityDamageEvent::CAUSE_VOID:
                    if (Functions::isGameIs($entity, "Hikabrain")) {
                        $hikabrain = Functions::getGameByHoster(Functions::getHoster($entity));
                        $event->setCancelled(true);
                        $entity->setHealth($entity->getMaxHealth());
                        if ($hikabrain->getStatus() === "§6en attente") {
                            $entity->teleport(new Position(-433.5, 68, -373.5, Server::getInstance()->getLevelByName($hikabrain->getWaitingRoom())));
                            NavigatorManager::sendNavigator($entity);
                        } elseif ($hikabrain->getStatus() === "§aen cours") {
                            $red = explode(";", Manager::POS_HIKABRAIN["Rouge"]);
                            $blue = explode(";", Manager::POS_HIKABRAIN["Bleu"]);
                            if ($hikabrain->getTeamOfPlayer($entity) === "Rouge") {
                                $pos = explode(":", $red[0]);
                                $entity->teleport(new Position($pos[0], $pos[1], $pos[2], Server::getInstance()->getLevelByName($hikabrain->getId())));
                            } elseif ($hikabrain->getTeamOfPlayer($entity) === "Bleu") {
                                $pos = explode(":", $blue[0]);
                                $entity->teleport(new Position($pos[0], $pos[1], $pos[2], Server::getInstance()->getLevelByName($hikabrain->getId())));
                            }
                            Hikabrain::sendStuff($entity);
                        }
                    }
                    break;
            }
        }
    }

    public function onPlace(BlockPlaceEvent $event)
    {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        if (Functions::isGameIs($player, "Hikabrain")) {
            $hikabrain = Functions::getGameByHoster(Functions::getHoster($player));
            if ($block->getY() >= 10) {
                if (!$event->isCancelled()) $event->setCancelled(true);
                $player->sendMessage(Manager::PREFIX_ALERT . "Vous ne pouvez pas poser de bloc ici.");
            } else {
                if (Server::getInstance()->isLevelLoaded($hikabrain->getId())) {
                    self::$blocks[$hikabrain->getId()][] = $block;
                }else{
                    unset(self::$blocks[$hikabrain->getId()]);
                }
            }
        }
    }

    public function onBreak(BlockBreakEvent $event)
    {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        if (Functions::isGameIs($player, "Hikabrain")) {
            if (!in_array($block->getId(), [24])) {
                if (!$event->isCancelled()) return $event->setCancelled(true);
            }
        }
    }

    public function onMove(PlayerMoveEvent $event)
    {
        $player = $event->getPlayer();
        if (Functions::isGameIs($player, "Hikabrain")) {
            $hikabrain = Functions::getGameByHoster(Functions::getHoster($player));
            $block = $player->getLevel()->getBlockAt($player->getFloorX(), $player->getFloorY(), $player->getFloorZ());
            if ($block->getId() === 26 && $block->getDamage() === 3 or $block->getId() === 26 && $block->getDamage() === 11) {
                if ($hikabrain->getTeamOfPlayer($player) === "Bleu") {
                    foreach (Server::getInstance()->getLevelByName(Hikabrain::getId($player))->getPlayers() as $player) {
                        if ($player instanceof Player) {
                            $player->sendMessage(Manager::ANONCEUR . "+1 point pour les §1bleu§f.");
                        }
                    }
                    $hikabrain->addPoint("Bleu", 1);
                    if ($hikabrain->getPoint("Rouge") >= $hikabrain->getPointForWin()) {
                        $hikabrain->stop("les §crouge §7ont gagner");
                        return;
                    } elseif ($hikabrain->getPoint("Bleu") >= $hikabrain->getPointForWin()) {
                        $hikabrain->stop( "les §1bleu §7ont gagner");
                        return;
                    }
                    if (isset(self::$blocks[$hikabrain->getId()])) {
                        foreach (self::$blocks[$hikabrain->getId()] as $id => $block) {
                            if ($block instanceof Block) {
                                $block->getLevel()->setBlock($block->asPosition(), new Air());
                            }
                        }
                    }
                    Main::getInstance()->getScheduler()->scheduleRepeatingTask(new RoundTask($hikabrain), 20);
                }
            }
            if ($block->getId() === 26 && $block->getDamage() === 1 or $block->getId() === 26 && $block->getDamage() === 9) {
                if ($hikabrain->getTeamOfPlayer($player) === "Rouge") {
                    foreach (Server::getInstance()->getLevelByName($hikabrain->getId())->getPlayers() as $player) {
                        if ($player instanceof Player) {
                            $player->sendMessage(Manager::ANONCEUR . "+1 point pour les §crouge§f.");
                        }
                    }
                    $hikabrain->addPoint("Rouge", 1);
                    if ($hikabrain->getPoint("Rouge") >= $hikabrain->getPointForWin()) {
                        $hikabrain->stop("les §crouge §7ont gagner");
                        return;
                    } elseif ($hikabrain->getPoint("Bleu") >= $hikabrain->getPointForWin()) {
                        $hikabrain->stop("les §1bleu §7ont gagner");
                        return;
                    }
                    if (isset(self::$blocks[$hikabrain->getId()])) {
                        foreach (self::$blocks[$hikabrain->getId()] as $id => $block) {
                            if ($block instanceof Block) {
                                $block->getLevel()->setBlock($block->asPosition(), new Air());
                            }
                        }
                    }
                    Main::getInstance()->getScheduler()->scheduleRepeatingTask(new RoundTask($hikabrain), 20);
                }
            }
        }
    }

    public function onDead(PlayerDeathEvent $event)
    {
        $entity = $event->getEntity();
        $cause = $entity->getLastDamageCause();
        $event->setDrops([]);
        $event->setDeathMessage(null);
        if ($cause instanceof EntityDamageByEntityEvent) {
            $damager = $cause->getDamager();
            if ($entity instanceof Player && $damager instanceof Player) {
                if (Functions::isGameIs($entity, "Hikabrain") && Functions::isGameIs($damager, "Hikabrain")) {
                    $hikabrain = Functions::getGameByHoster(Functions::getHoster($damager));
                    $hikabrain->broadcastMessage("§d" . $damager->getName() . " §fvient de tué §d" . $entity->getName() . " §7[" . $damager->getHealth() . "/" . $damager->getMaxHealth() . "]");
                    $damager->setHealth($damager->getMaxHealth());
                }
            }
        }
    }

    public function onRespawn(PlayerRespawnEvent $event)
    {
        $player = $event->getPlayer();
        if (Functions::isGameIs($player, "Hikabrain")) {
            $hikabrain = Functions::getGameByHoster(Functions::getHoster($player));
            $player->setHealth($player->getMaxHealth());
            $red = explode(";", Manager::POS_HIKABRAIN["Rouge"]);
            $blue = explode(";", Manager::POS_HIKABRAIN["Bleu"]);
            if ($hikabrain->getTeamOfPlayer($player) === "Rouge") {
                $pos = explode(":", $red[0]);
                $event->setRespawnPosition(new Position($pos[0], $pos[1], $pos[2], Server::getInstance()->getLevelByName(Hikabrain::getId($player))));
            }
            if ($hikabrain->getTeamOfPlayer($player) === "Bleu") {
                $pos = explode(":", $blue[0]);
                $event->setRespawnPosition(new Position($pos[0], $pos[1], $pos[2], Server::getInstance()->getLevelByName(Hikabrain::getId($player))));
            }
            Hikabrain::sendStuff($player);
        }
    }

    public function onChat(PlayerChatEvent $event){
        $player = $event->getPlayer();
        $message = $event->getMessage();
        if (Functions::isGameIs($player, "Hikabrain")) {
            $hikabrain = Functions::getGameByHoster(Functions::getHoster($player));
            if ($hikabrain->getType() === "2v2") {
                if ($hikabrain->getStatus() === "§aen cours") {
                    if (substr($message, 0, 1) === "@") {
                        $hikabrain->sendMessageForAll($player, $message);
                        if (!$event->isCancelled()) $event->setCancelled(true);
                    } else {
                        $hikabrain->sendMessageForTeam($hikabrain->getTeamOfPlayer($player), $player, $message);
                        if (!$event->isCancelled()) $event->setCancelled(true);
                    }
                }
            }else{
                if ($hikabrain->getStatus() === "§aen cours") {
                    $hikabrain->broadcastMessage("§7" . $player->getName() . " - " . $message);
                    if (!$event->isCancelled()) $event->setCancelled(true);
                }
            }
        }
    }

    public function onEntityDamageByEntity(EntityDamageByEntityEvent $event){
        $entity = $event->getEntity();
        $damager = $event->getDamager();
        if ($entity instanceof Player && $damager instanceof Player){
            if (Functions::isGameIs($entity, "Hikabrain") && Functions::isGameIs($damager, "Hikabrain")){
                $hikabrain = Functions::getGameByHoster(Functions::getHoster($damager));
                if ($hikabrain->getTeamOfPlayer($damager) === $hikabrain->getTeamOfPlayer($entity)){
                    if (!$event->isCancelled()) return $event->setCancelled(true);
                }
            }
        }
    }

}