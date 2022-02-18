<?php

namespace Zoumi\MiniGame\tasks\gunplay;

use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use Zoumi\MiniGame\api\game\GunPlay;
use Zoumi\MiniGame\Main;
use Zoumi\MiniGame\Manager;
use Zoumi\MiniGame\utils\Functions;

class WaitingRoomGP extends Task {

    /** @var $gunplay */
    private $gunplay;
    /** @var int $time */
    public $time = 11;

    public function __construct(GunPlay $gunplay)
    {
        $this->gunplay = $gunplay;
    }

    public function onRun(int $currentTick)
    {
        $gunplay = $this->gunplay;
        if (isset(Main::$game["GunPlay"][$gunplay->getHoster()->getName()])) {
            /** Si il a 0 joueur on stop */
            if ($gunplay->getPlayersCount() < 1){
                $gunplay->stop("manque de joueur");
                Main::getInstance()->getScheduler()->cancelTask($this->getTaskId());
            }
            if ($gunplay->getPlayersCount() >= $gunplay->getMinPlayers()) {
                if (--$this->time === 0) {
                    foreach ($gunplay->getPlayersListArray() as $player) {
                        $p = Server::getInstance()->getPlayerExact($player);
                        if ($p instanceof Player) {
                            $p->getInventory()->clearAll();
                            $p->getArmorInventory()->clearAll();
                            $gunplay->sendWeapon($p, $gunplay->getPoint($p));
                            $p->setHealth($p->getMaxHealth());
                            $p->setImmobile(false);
                            $p->addSubTitle("§l§aFight !");
                        }
                    }
                    $gunplay->setStatus("§aen cours");
                    $gunplay->initTime(15);
                    Main::getInstance()->getScheduler()->scheduleRepeatingTask(new GunPlayTask($gunplay), 20);
                    Main::getInstance()->getScheduler()->cancelTask($this->getTaskId());
                } elseif ($this->time === 10) {
                    $i = 0;
                    foreach ($gunplay->getPlayersListArray() as $player) {
                        $p = Server::getInstance()->getPlayerExact($player);
                        if ($p instanceof Player) {
                            $pos = explode(":", Manager::POS_GUNPLAY[$i]);
                            $p->teleport(new Position($pos[0], $pos[1], $pos[2], Server::getInstance()->getLevelByName($gunplay->getId())));
                            $p->setImmobile(true);
                            Functions::sendSound($p, "note.chime");
                            $p->sendPopup(Manager::ANONCEUR . "Commence dans §a" . $this->time . " §fseconde(s).");
                            $i++;
                        }
                    }
                } elseif ($this->time <= 3) {
                    foreach ($gunplay->getPlayersListArray() as $player) {
                        $p = Server::getInstance()->getPlayerExact($player);
                        if ($p instanceof Player) {
                            $p->setImmobile(true);
                            Functions::sendSound($p, "note.bell");
                            $p->sendPopup(Manager::ANONCEUR . "Commence dans §c" . $this->time . " §fseconde(s).");
                        }
                    }
                } elseif ($this->time <= 5) {
                    foreach ($gunplay->getPlayersListArray() as $player) {
                        $p = Server::getInstance()->getPlayerExact($player);
                        if ($p instanceof Player) {
                            $p->setImmobile(true);
                            Functions::sendSound($p, "note.bell");
                            $p->sendPopup(Manager::ANONCEUR . "Commence dans §6" . $this->time . " §fseconde(s).");
                        }
                    }
                } elseif ($this->time <= 9) {
                    foreach ($gunplay->getPlayersListArray() as $player) {
                        $p = Server::getInstance()->getPlayerExact($player);
                        if ($p instanceof Player) {
                            $p->setImmobile(true);
                            Functions::sendSound($p, "note.chime");
                            $p->sendPopup(Manager::ANONCEUR . "Commence dans §a" . $this->time . " §fseconde(s).");
                        }
                    }
                }
            } else {
                foreach ($gunplay->getPlayersListArray() as $player) {
                    $p = Server::getInstance()->getPlayerExact($player);
                    if ($p instanceof Player) {
                        $p->sendPopup(Manager::ANONCEUR . "En attente de §c" . ($gunplay->getMinPlayers() - $gunplay->getPlayersCount()) . " §fjoueur(s) suplémentaire.");
                    }
                }
            }
        }else Main::getInstance()->getScheduler()->cancelTask($this->getTaskId());
    }

}