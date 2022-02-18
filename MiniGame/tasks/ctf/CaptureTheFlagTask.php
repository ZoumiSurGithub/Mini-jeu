<?php

namespace Zoumi\MiniGame\tasks\ctf;

use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use Zoumi\MiniGame\api\game\CaptureTheFlag;
use Zoumi\MiniGame\api\Scoreboard;
use Zoumi\MiniGame\api\game\TeamManager;
use Zoumi\MiniGame\Main;

class CaptureTheFlagTask extends Task {

    /** @var string $id */
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function onRun(int $currentTick)
    {
        $id = $this->id;
        if (isset(CaptureTheFlag::$ctf[$id])){
            /** Time */
            if (time() >= CaptureTheFlag::$ctf[$id]["time"]["left"]) {
                CaptureTheFlag::stopCaptureTheFlag($id, "temps écoulé");
                Main::getInstance()->getScheduler()->cancelTask($this->getTaskId());
                return;
            }

            /* Verif du nombre de joueur */
            if (TeamManager::getPlayerCount($id, "Rouge") < 1){
                CaptureTheFlag::stopCaptureTheFlag($id, "l'équipe §cRouge §7a déclarer forfait");
                Main::getInstance()->getScheduler()->cancelTask($this->getTaskId());
                return;
            }elseif (TeamManager::getPlayerCount($id, "Bleu") < 1){
                CaptureTheFlag::stopCaptureTheFlag($id, "l'équipe §1Bleu §7a déclarer forfait");
                Main::getInstance()->getScheduler()->cancelTask($this->getTaskId());
                return;
            }

            /* Verif des points */
            if (TeamManager::getPoint($this->id, "Rouge") >= 10) {
                CaptureTheFlag::stopCaptureTheFlag($this->id, "les §crouge §7ont gagner");
                Main::getInstance()->getScheduler()->cancelTask($this->getTaskId());
                return;
            } elseif (TeamManager::getPoint($this->id, "Bleu") >= 10) {
                CaptureTheFlag::stopCaptureTheFlag($this->id, "les §1bleu §7ont gagner");
                Main::getInstance()->getScheduler()->cancelTask($this->getTaskId());
                return;
            }

            /* Scoreboard */
            foreach (CaptureTheFlag::$ctf[$this->id]["players"] as $player => $points) {
                $player = Server::getInstance()->getPlayerExact($player);
                if ($player instanceof Player) {
                    $scoreboard = CaptureTheFlag::$scoreboard[$player->getName()] = new Scoreboard($player);
                    $scoreboard
                        ->setLine(0, "      ")
                        ->setLine(1, "§710 §fpoints pour gagner.")
                        ->setLine(2, "              ")
                        ->setLine(3, "§cRouge: §7" . TeamManager::getPoint($this->id, "Rouge"))
                        ->setLine(4, "§1Bleu: §7" . TeamManager::getPoint($this->id, "Bleu"))
                        ->setLine(5, "          ")
                        ->setLine(6, "§fVous êtes " . TeamManager::getTeamOfPlayer($this->id, $player, true) . "§f.")
                        ->setLine(7, "                      ")
                        ->setLine(8, "§7sunparadise-mc.eu")
                        ->set();
                }
            }

        }else Main::getInstance()->getScheduler()->cancelTask($this->getTaskId());
    }

}