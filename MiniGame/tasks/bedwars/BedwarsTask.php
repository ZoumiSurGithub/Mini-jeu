<?php

namespace Zoumi\MiniGame\tasks\bedwars;

use pocketmine\level\Explosion;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use Zoumi\MiniGame\api\game\Bedwars;
use Zoumi\MiniGame\api\Scoreboard;
use Zoumi\MiniGame\api\game\TeamManager;
use Zoumi\MiniGame\Main;

class BedwarsTask extends Task {

    /** @var string $id */
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function onRun(int $currentTick)
    {
        $id = $this->id;
        if (isset(Bedwars::$bedwars[$id])){
            /** Time */
            $left = Bedwars::$bedwars[$id]["time"]["left"];
            var_dump($left . "   " . $currentTick);
            $bedExplose = Bedwars::$bedwars[$id]["time"]["bedExplose"];
            if (time() <= $left) {
                if (Main::getInstance()->convertGame($left - time()) === "29m:30s"){
                    if (TeamManager::isBedAlive($id, "Rouge")){
                        TeamManager::setBedAlive($id, "Rouge", false);
                        TeamManager::sendTitle($id, "Rouge", "§cLit détruit", "Temps écoulé.");
                        TeamManager::sendSound($id, "Rouge", "beacon.deactivate");
                        $explode = new Explosion(new Position(-473.5, 66, -278.5, Server::getInstance()->getLevelByName($id)), 4);
                        $explode->explodeA();
                        $explode->explodeB();
                    }
                    if (TeamManager::isBedAlive($id, "Bleu")){
                        TeamManager::setBedAlive($id, "Bleu", false);
                        TeamManager::sendTitle($id, "Bleu", "§cLit détruit", "Temps écoulé.");
                        TeamManager::sendSound($id, "Bleu", "beacon.deactivate");
                        $explode = new Explosion(new Position(-539.5, 66, -340.5, Server::getInstance()->getLevelByName($id)), 4);
                        $explode->explodeA();
                        $explode->explodeB();
                    }
                    if (TeamManager::isBedAlive($id, "Jaune")){
                        TeamManager::setBedAlive($id, "Jaune", false);
                        TeamManager::sendTitle($id, "Jaune", "§cLit détruit", "Temps écoulé.");
                        TeamManager::sendSound($id, "Jaune", "beacon.deactivate");
                        $explode = new Explosion(new Position(-412.5, 66, -344.5, Server::getInstance()->getLevelByName($id)), 4);
                        $explode->explodeA();
                        $explode->explodeB();
                    }
                    if (TeamManager::isBedAlive($id, "Vert")){
                        TeamManager::setBedAlive($id, "Vert", false);
                        TeamManager::sendTitle($id, "Vert", "§cLit détruit", "Temps écoulé.");
                        TeamManager::sendSound($id, "Vert", "beacon.deactivate");
                        $explode = new Explosion(new Position(-477.5, 66, -405.5, Server::getInstance()->getLevelByName($id)), 4);
                        $explode->explodeA();
                        $explode->explodeB();
                    }
                    return true;
                }
            }else{
                Bedwars::stopBedwars($id, "temps écoulé");
                Main::getInstance()->getScheduler()->cancelTask($this->getTaskId());
                return;
            }
            
            /* Scoreboard */
            foreach (Bedwars::$bedwars[$id]["players"] as $player => $pla){
                $p = Server::getInstance()->getPlayerExact($player);
                if ($p instanceof Player){
                    $scoreboard = Bedwars::$scoreboard[$p->getName()] = new Scoreboard($p);
                    $scoreboard
                        ->setLine(0, "      ")
                        ->setLine(1, "Fin dans §7" . Main::getInstance()->convertGame(Bedwars::$bedwars[$id]["time"]["left"] - time()))
                        ->setLine(2, "              ")
                        ->setLine(3, "§cRouge: " . TeamManager::isBedAlive($id, "Rouge"))
                        ->setLine(4, "§1Bleu: " . TeamManager::isBedAlive($id, "Bleu"))
                        ->setLine(5, "§eJaune: " . TeamManager::isBedAlive($id, "Jaune"))
                        ->setLine(6, "§2Vert: " . TeamManager::isBedAlive($id, "Vert"))
                        ->setLine(7, "                  ")
                        ->setLine(8, "§7sunparadise-mc.eu")
                        ->set();
                }
            }
            return true;
        }else Main::getInstance()->getScheduler()->cancelTask($this->getTaskId());
    }

}