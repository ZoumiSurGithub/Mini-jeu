<?php

namespace Zoumi\MiniGame\listeners\events;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\Server;
use Zoumi\MiniGame\api\game\CaptureTheFlag;
use Zoumi\MiniGame\api\game\TeamManager;
use Zoumi\MiniGame\Manager;

class CaptureTheFlagEvent implements Listener
{

    public function onQuit(PlayerQuitEvent $event)
    {
        $player = $event->getPlayer();
        if (CaptureTheFlag::isInGame($player)) {
            CaptureTheFlag::removePlayer(CaptureTheFlag::getId($player), $player);
        }
    }

    public function onRespawn(PlayerRespawnEvent $event)
    {
        $player = $event->getPlayer();
        if (CaptureTheFlag::isInGame($player)) {
            if (CaptureTheFlag::getStatus(CaptureTheFlag::getId($player)) === "§aen cours") {
                if (TeamManager::getTeamOfPlayer(CaptureTheFlag::getId($player), $player) === "Rouge") {
                    $pos = explode(";", CaptureTheFlag::$pos["Rouge"]);
                    $pos = explode(":", $pos[mt_rand(0, 5)]);
                    $event->setRespawnPosition(new Position($pos[0], $pos[1], $pos[2], Server::getInstance()->getLevelByName(CaptureTheFlag::getId($player))));
                }
                if (TeamManager::getTeamOfPlayer(CaptureTheFlag::getId($player), $player) === "Bleu") {
                    $pos = explode(";", CaptureTheFlag::$pos["Bleu"]);
                    $pos = explode(":", $pos[mt_rand(0, 5)]);
                    $event->setRespawnPosition(new Position($pos[0], $pos[1], $pos[2], Server::getInstance()->getLevelByName(CaptureTheFlag::getId($player))));
                }
                CaptureTheFlag::sendStuff($player);
            }elseif (CaptureTheFlag::getStatus(CaptureTheFlag::getId($player)) === "§6en attente"){
                $event->setRespawnPosition(Server::getInstance()->getLevelByName(CaptureTheFlag::$ctf[CaptureTheFlag::getId($player)]["WaitingRoomGP"])->getSafeSpawn());
            }
        }
    }

    public function onMove(PlayerMoveEvent $event)
    {
        $player = $event->getPlayer();
        if (CaptureTheFlag::isInGame($player)) {
            $block = $player->getLevel()->getBlockAt($player->getFloorX(), $player->getFloorY(), $player->getFloorZ());
            $id = CaptureTheFlag::getId($player);
            /** Rouge */
            if ($block->getId() === 236 && $block->getDamage() === 14) {
                if (TeamManager::getTeamOfPlayer(CaptureTheFlag::getId($player), $player) === "Bleu") {
                    if (CaptureTheFlag::hasFlagRed(CaptureTheFlag::getId($player)) === "Aucun.") {
                        CaptureTheFlag::setHasFlag(CaptureTheFlag::getId($player), "Red", $player->getName());
                        foreach (Server::getInstance()->getLevelByName(CaptureTheFlag::getId($player))->getPlayers() as $p) {
                            if ($p instanceof Player) {
                                $p->sendMessage(Manager::ANONCEUR . "§b" . $player->getName() . " §fvient de capturer le drapeau §cRouge§f.");
                            }
                        }
                    }
                }
                if (TeamManager::getTeamOfPlayer(CaptureTheFlag::getId($player), $player) === "Rouge") {
                    if (CaptureTheFlag::hasFlagBlue(CaptureTheFlag::getId($player)) !== "Aucun.") {
                        $p = Server::getInstance()->getPlayerExact(CaptureTheFlag::hasFlagBlue(CaptureTheFlag::getId($player)));
                        if ($p instanceof Player) {
                            if ($p->getName() === $player->getName()) {
                                CaptureTheFlag::setHasFlag(CaptureTheFlag::getId($player), "Blue", "Aucun.");
                                TeamManager::addPoint(CaptureTheFlag::getId($player), "Rouge", 1);
                                foreach (Server::getInstance()->getLevelByName(CaptureTheFlag::getId($player))->getPlayers() as $player) {
                                    $player->sendMessage(Manager::ANONCEUR . "§b" . $p->getName() . " §fvient de faire gagner 1 point à l'équipe §crouge§f.");
                                }
                            }
                        }
                    }
                }
            }
            /** Bleu */
            if ($block->getId() === 236 && $block->getDamage() === 11) {
                if (TeamManager::getTeamOfPlayer(CaptureTheFlag::getId($player), $player) === "Rouge") {
                    if (CaptureTheFlag::hasFlagBlue(CaptureTheFlag::getId($player)) === "Aucun.") {
                        CaptureTheFlag::setHasFlag(CaptureTheFlag::getId($player), "Blue", $player->getName());
                        foreach (Server::getInstance()->getLevelByName(CaptureTheFlag::getId($player))->getPlayers() as $p) {
                            if ($p instanceof Player) {
                                $p->sendMessage(Manager::ANONCEUR . "§b" . $player->getName() . " §fvient de capturer le drapeau §1Bleu§f.");
                            }
                        }
                    }
                }
                if (TeamManager::getTeamOfPlayer(CaptureTheFlag::getId($player), $player) === "Bleu") {
                    if (CaptureTheFlag::hasFlagRed(CaptureTheFlag::getId($player)) !== "Aucun.") {
                        $p = Server::getInstance()->getPlayerExact(CaptureTheFlag::hasFlagRed(CaptureTheFlag::getId($player)));
                        if ($p instanceof Player) {
                            if ($p->getName() === $player->getName()) {
                                CaptureTheFlag::setHasFlag(CaptureTheFlag::getId($player), "Red", "Aucun.");
                                TeamManager::addPoint(CaptureTheFlag::getId($player), "Bleu", 1);
                                foreach (Server::getInstance()->getLevelByName(CaptureTheFlag::getId($player))->getPlayers() as $player) {
                                    $player->sendMessage(Manager::ANONCEUR . "§b" . $p->getName() . " §fvient de faire gagner 1 point à l'équipe §1bleu§f.");
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function onEntityDamageByEntity(EntityDamageByEntityEvent $event){
        $entity = $event->getEntity();
        $damager = $event->getDamager();
        if ($entity instanceof Player && $damager instanceof Player){
            if (CaptureTheFlag::isInGame($entity) && CaptureTheFlag::isInGame($damager)){
                if (TeamManager::getTeamOfPlayer(CaptureTheFlag::getId($damager), $damager) === TeamManager::getTeamOfPlayer(CaptureTheFlag::getId($entity), $entity)){
                    if (!$event->isCancelled()) return $event->setCancelled(true);
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
                if (CaptureTheFlag::isInGame($entity)) {
                    if (CaptureTheFlag::hasFlagRed(CaptureTheFlag::$players[$entity->getName()]) === $entity->getName()) {
                        CaptureTheFlag::$ctf[CaptureTheFlag::$players[$entity->getName()]]["hasFlagRed"] = "Aucun.";
                    }
                    if (CaptureTheFlag::hasFlagBlue(CaptureTheFlag::$players[$entity->getName()]) === $entity->getName()) {
                        CaptureTheFlag::$ctf[CaptureTheFlag::$players[$entity->getName()]]["hasFlagBlue"] = "Aucun.";
                    }
                    foreach (Server::getInstance()->getLevelByName(CaptureTheFlag::$players[$damager->getName()])->getPlayers() as $player) {
                        if ($player instanceof Player) {
                            $player->sendMessage("§d" . $damager->getName() . " §fvient de tué §d" . $entity->getName() . " §7[" . $damager->getHealth() . "/" . $damager->getMaxHealth() . "]");
                        }

                    }
                }
            }
        }
    }

    public function onChat(PlayerChatEvent $event){
        $player = $event->getPlayer();
        $message = $event->getMessage();
        if (CaptureTheFlag::isInGame($player)) {
            if (CaptureTheFlag::getStatus(CaptureTheFlag::getId($player)) === "§aen cours") {
                if (substr($message, 0, 1) === "@") {
                    CaptureTheFlag::sendMessageForAll(CaptureTheFlag::getId($player), $player, $message);
                    if (!$event->isCancelled()) $event->setCancelled(true);
                } else {
                    TeamManager::sendMessageForTeam(CaptureTheFlag::getId($player), TeamManager::getTeamOfPlayer(CaptureTheFlag::getId($player), $player), $player, $message);
                    if (!$event->isCancelled()) $event->setCancelled(true);
                }
            }
        }
    }
    
}