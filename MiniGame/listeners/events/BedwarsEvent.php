<?php

namespace Zoumi\MiniGame\listeners\events;

use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\Server;
use Zoumi\MiniGame\api\game\Bedwars;
use Zoumi\MiniGame\api\game\TeamManager;
use Zoumi\MiniGame\Manager;

class BedwarsEvent implements Listener {

    public function onMove(PlayerMoveEvent $event){
        $player = $event->getPlayer();
        if (isset(Bedwars::$players[$player->getName()])) {
            foreach (Server::getInstance()->getLevelByName(Bedwars::$players[$player->getName()])->getEntities() as $entity) {
                if ($entity instanceof Living) {
                    if ($entity->getLocation()->getLevel()->getFolderName() === $player->getLevel()->getFolderName()) {
                        if ($entity->getLocation()->distance($player->getLocation()) <= 5) {
                            $entity->lookAt($player);
                        }
                    }
                }
            }
        }
    }

    public function onBreak(BlockBreakEvent $event){
        $player = $event->getPlayer();
        $block = $event->getBlock();
        if (isset(Bedwars::$players[$player->getName()])){
            $id = Bedwars::$players[$player->getName()];
            /* Rouge */
            if ($block->getId() === 26 && $block->getDamage() === 0 || $block->getDamage() === 8){
                if (TeamManager::getTeamOfPlayer($id, $player) === "Rouge"){
                    $player->sendMessage(Manager::PREFIX_ALERT . "Vous ne pouvez pas casser le lit de votre propre équipe !");
                    if (!$event->isCancelled()) return $event->setCancelled(true);
                }else{
                    TeamManager::sendTitle($id, "Rouge", "§cLit détruit", "Votre lit a été détruit par l'équipe " . TeamManager::getTeamOfPlayer($id, $player, true) . " §f!");
                    TeamManager::setBedAlive($id, "Rouge", false);
                    TeamManager::sendSound($id, "Rouge", "beacon.deactivate");
                }

            }
            /* Bleu */
            if ($block->getId() === 26 && $block->getDamage() === 1 || $block->getDamage() === 9){
                if (TeamManager::getTeamOfPlayer($id, $player) === "Bleu"){
                    $player->sendMessage(Manager::PREFIX_ALERT . "Vous ne pouvez pas casser le lit de votre propre équipe !");
                    if (!$event->isCancelled()) return $event->setCancelled(true);
                }else{
                    TeamManager::sendTitle($id, "Bleu", "§cLit détruit", "Votre lit a été détruit par l'équipe " . TeamManager::getTeamOfPlayer($id, $player, true) . " §f!");
                    TeamManager::setBedAlive($id, "Bleu", false);
                    TeamManager::sendSound($id, "Bleu", "beacon.deactivate");
                }
            }
            /* Vert */
            if ($block->getId() === 26 && $block->getDamage() === 2 || $block->getDamage() === 10){
                if (TeamManager::getTeamOfPlayer($id, $player) === "Vert"){
                    $player->sendMessage(Manager::PREFIX_ALERT . "Vous ne pouvez pas casser le lit de votre propre équipe !");
                    if (!$event->isCancelled()) return $event->setCancelled(true);
                }else{
                    TeamManager::sendTitle($id, "Vert", "§cLit détruit", "Votre lit a été détruit par l'équipe " . TeamManager::getTeamOfPlayer($id, $player, true) . " §f!");
                    TeamManager::setBedAlive($id, "Vert", false);
                    TeamManager::sendSound($id, "Vert", "beacon.deactivate");
                }
            }
            /* Jaune */
            if ($block->getId() === 26 && $block->getDamage() === 3 || $block->getDamage() === 11){
                if (TeamManager::getTeamOfPlayer($id, $player) === "Jaune"){
                    $player->sendMessage(Manager::PREFIX_ALERT . "Vous ne pouvez pas casser le lit de votre propre équipe !");
                    if (!$event->isCancelled()) return $event->setCancelled(true);
                }else{
                    TeamManager::sendTitle($id, "Jaune", "§cLit détruit", "Votre lit a été détruit par l'équipe " . TeamManager::getTeamOfPlayer($id, $player, true) . " §f!");
                    TeamManager::setBedAlive($id, "Jaune", false);
                    TeamManager::sendSound($id, "Jaune", "beacon.deactivate");
                }
            }
        }
    }

}