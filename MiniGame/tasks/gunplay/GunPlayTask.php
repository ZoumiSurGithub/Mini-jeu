<?php

namespace Zoumi\MiniGame\tasks\gunplay;

use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use Zoumi\MiniGame\api\game\GunPlay;
use Zoumi\MiniGame\api\Scoreboard;
use Zoumi\MiniGame\Main;

class GunPlayTask extends Task {

    private $gunplay;

    public function __construct(GunPlay $gunplay)
    {
        $this->gunplay = $gunplay;
    }

    public function onRun(int $currentTick)
    {
        $gunplay = $this->gunplay;
        /* Si il n'a pas minimum 2 joueurs */
        if ($gunplay->getPlayersCount() < 2){
            $gunplay->stop("le nombre de joueur est inférieur à 2");
            Main::getInstance()->getScheduler()->cancelTask($this->getTaskId());
            return;
        }

        /* Temps écoulé ? */
        if (time() >= $gunplay->getTime()){
            $gunplay->stop("temps écoulé");
            Main::getInstance()->getScheduler()->cancelTask($this->getTaskId());
            return;
        }

        /* Verif des points */
        foreach ($gunplay->players as $player => $points){
            $player = Server::getInstance()->getPlayerExact($player);
            if ($player instanceof Player) {
                if ($points >= 250) {
                    $xp = mt_rand(5, 50);
                    $coins = mt_rand(10, 100);
                    $gunplay->addPrize($player, $xp, $coins);
                    $gunplay->stop("§e" . $player->getName() . " §7a gagner la partie");
                    Main::getInstance()->getScheduler()->cancelTask($this->getTaskId());
                    return;
                }
            }
        }

        /* Scoreboard */
        foreach ($gunplay->players as $player => $points) {
            $player = Server::getInstance()->getPlayerExact($player);
            if ($player instanceof Player) {
                $scoreboard = GunPlay::$scoreboard[$player->getName()] = new Scoreboard($player);
                $scoreboard
                    ->setLine(0, "      ")
                    ->setLine(1, "§7Top 1")
                    ->setLine(2, "§6➥ §e" . $gunplay->getTopOne())
                    ->setLine(3, "              ")
                    ->setLine(4, "§fVous êtes à §7" . $gunplay->getPointOfPlayer($player) . " §fpoints.")
                    ->setLine(5, "          ")
                    ->setLine(6, "§7sunparadise-mc.eu")
                    ->set();
            }
        }
    }

}