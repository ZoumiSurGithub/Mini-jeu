<?php

namespace Zoumi\MiniGame\tasks\hikabrain;

use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use Zoumi\MiniGame\api\game\Hikabrain;
use Zoumi\MiniGame\api\game\HikabrainInstance;
use Zoumi\MiniGame\api\Scoreboard;
use Zoumi\MiniGame\api\game\TeamManager;
use Zoumi\MiniGame\Main;

class HikabrainTask extends Task {

    private $hikabrain;

    public function __construct(HikabrainInstance $hikabrainInstance)
    {
        $this->hikabrain = $hikabrainInstance;
    }

    public function onRun(int $currentTick)
    {
        $hikabrain = $this->hikabrain;
        /* Temps */
        if (time() >= $hikabrain->getTime()) {
            $hikabrain->stop( "temps écoulé");
            Main::getInstance()->getScheduler()->cancelTask($this->getTaskId());
            return;
        }

        /* Verif du nombre de joueur */
        /*
        if ($hikabrain->getPlayerCount("Rouge") < 1) {
            $hikabrain->stop("manque de joueur dans la team adverse");
            Main::getInstance()->getScheduler()->cancelTask($this->getTaskId());
            return;
        } elseif ($hikabrain->getPlayerCount("Bleu") < 1) {
            $hikabrain->stop("manque de joueur dans la team adverse");
            Main::getInstance()->getScheduler()->cancelTask($this->getTaskId());
            return;
        }
        */

        /* Verif des points */
        if ($hikabrain->getPoint("Rouge") >= $hikabrain->getPointForWin()) {
            $xp = mt_rand(5, 50);
            $coins = mt_rand(10, 100);
            foreach ($hikabrain->getPlayersListByTeamArray("Rouge") as $player){
                $p = Server::getInstance()->getPlayerExact($player);
                if ($p instanceof Player) {
                    $hikabrain->addPrize($p, $xp, $coins);
                }
            }
            $hikabrain->stop("les §crouge §7ont gagner");
            Main::getInstance()->getScheduler()->cancelTask($this->getTaskId());
            return;
        } elseif ($hikabrain->getPoint("Bleu") >= $hikabrain->getPointForWin()) {
            $xp = mt_rand(5, 50);
            $coins = mt_rand(10, 100);
            foreach ($hikabrain->getPlayersListByTeamArray("Bleu") as $player){
                $p = Server::getInstance()->getPlayerExact($player);
                if ($p instanceof Player) {
                    $hikabrain->addPrize($p, $xp, $coins);
                }
            }
            Hikabrain::stopHikabrain($this->id, "les §1bleu §7ont gagner");
            Main::getInstance()->getScheduler()->cancelTask($this->getTaskId());
            return;
        }

        /* Scoreboard */
        foreach ($hikabrain->getPlayersListArray() as $player) {
            $player = Server::getInstance()->getPlayerExact($player);
            if ($player instanceof Player) {
                $scoreboard = $hikabrain::$scoreboard = new Scoreboard($player);
                $scoreboard
                    ->setLine(0, "      ")
                    ->setLine(1, "§7" . $hikabrain->getPointForWin() . " §fpoints pour gagner.")
                    ->setLine(2, "              ")
                    ->setLine(3, "§cRouge: §7" . $hikabrain->getPoint("Rouge"))
                    ->setLine(4, "§1Bleu: §7" . $hikabrain->getPoint("Bleu"))
                    ->setLine(5, "          ")
                    ->setLine(6, "§fVous êtes " . $hikabrain->getTeamOfPlayer($player, true) . "§f.")
                    ->setLine(7, "                      ")
                    ->setLine(8, "§7sunparadise-mc.eu")
                    ->set();
            }
        }
    }

}