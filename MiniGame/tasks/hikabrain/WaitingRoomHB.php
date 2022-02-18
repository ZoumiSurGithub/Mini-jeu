<?php

namespace Zoumi\MiniGame\tasks\hikabrain;

use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use Zoumi\MiniGame\api\game\Hikabrain;
use Zoumi\MiniGame\api\game\HikabrainInstance;
use Zoumi\MiniGame\api\game\TeamManager;
use Zoumi\MiniGame\Main;
use Zoumi\MiniGame\Manager;
use Zoumi\MiniGame\utils\Functions;

class WaitingRoomHB extends Task {

    /** @var string $hikabrain */
    public $hikabrain;
    /** @var int $time */
    public $time = 11;

    public function __construct(HikabrainInstance $hikabrainInstance)
    {
        $this->hikabrain = $hikabrainInstance;
    }

    public function onRun(int $currentTick)
    {
        $hikabrain = $this->hikabrain;
        if ($hikabrain->getPlayersCount() <= $hikabrain->getMinPlayers()) {
            if (--$this->time === 0) {
                foreach ($hikabrain->getPlayersListArray() as $player) {
                    $p = Server::getInstance()->getPlayerExact($player);
                    if ($p instanceof Player) {
                        $hikabrain->sendStuff($p);
                        $p->setImmobile(false);
                        Functions::sendSound($p, "beacon.activate");
                        $p->addSubTitle("§l§aFight !");
                    }
                }
                $hikabrain->setStatus("§aen cours");
                $hikabrain->initTime(10);
                Main::getInstance()->getScheduler()->scheduleRepeatingTask(new HikabrainTask($hikabrain), 20);
                Main::getInstance()->getScheduler()->cancelTask($this->getTaskId());
            } elseif ($this->time === 10) {
                foreach ($hikabrain->getPlayersListArray() as $player) {
                    $p = Server::getInstance()->getPlayerExact($player);
                    if ($p instanceof Player) {
                        if (!$hikabrain->isInTeam($p)) {
                            $hikabrain->setRandomTeamForNewPlayer($p);
                        }
                        $p->setImmobile(true);
                        Functions::sendSound($p, "note.chime");
                        $p->sendPopup(Manager::ANONCEUR . "Commence dans §a" . Main::getInstance()->convert($this->time) . " §fseconde(s).");
                    }
                }
                $red = explode(";", Manager::POS_HIKABRAIN["Rouge"]);
                $redcount = 0;
                $blue = explode(";", Manager::POS_HIKABRAIN["Bleu"]);
                $bluecount = 0;
                foreach ($hikabrain->getPlayersListByTeamArray("Rouge") as $player) {
                    $p = Server::getInstance()->getPlayerExact($player);
                    if ($p instanceof Player) {
                        $pos = explode(":", $red[$redcount]);
                        $p->teleport(new Position($pos[0], $pos[1], $pos[2], Server::getInstance()->getLevelByName($hikabrain->getId())));
                        $redcount++;
                    }
                }
                foreach ($hikabrain->getPlayersListByTeamArray("Bleu") as $player) {
                    $p = Server::getInstance()->getPlayerExact($player);
                    if ($p instanceof Player) {
                        $pos = explode(":", $blue[$bluecount]);
                        $p->teleport(new Position($pos[0], $pos[1], $pos[2], Server::getInstance()->getLevelByName($hikabrain->getId())));
                        $bluecount++;
                    }
                }
            } elseif ($this->time <= 3) {
                foreach ($hikabrain->getPlayersListArray() as $player) {
                    $p = Server::getInstance()->getPlayerExact($player);
                    if ($p instanceof Player) {
                        $p->setImmobile(true);
                        Functions::sendSound($p, "note.bell");
                        $p->sendPopup(Manager::ANONCEUR . "Commence dans §c" . Main::getInstance()->convert($this->time) . " §fseconde(s).");
                    }
                }
            } elseif ($this->time <= 5) {
                foreach ($hikabrain->getPlayersListArray() as $player) {
                    $p = Server::getInstance()->getPlayerExact($player);
                    if ($p instanceof Player) {
                        $p->setImmobile(true);
                        Functions::sendSound($p, "note.bell");
                        $p->sendPopup(Manager::ANONCEUR . "Commence dans §6" . Main::getInstance()->convert($this->time) . " §fseconde(s).");
                    }
                }
            } elseif ($this->time <= 9) {
                foreach ($hikabrain->getPlayersListArray() as $player) {
                    $p = Server::getInstance()->getPlayerExact($player);
                    if ($p instanceof Player) {
                        $p->setImmobile(true);
                        Functions::sendSound($p, "note.chime");
                        $p->sendPopup(Manager::ANONCEUR . "Commence dans §a" . Main::getInstance()->convert($this->time) . " §fseconde(s).");
                    }
                }
            }
        } else {
            foreach ($hikabrain->getPlayersListArray() as $player) {
                $p = Server::getInstance()->getPlayerExact($player);
                if ($p instanceof Player) {
                    $p->sendPopup(Manager::ANONCEUR . "En attente de §c" . ($hikabrain->getMinPlayers() - $hikabrain->getPlayersCount()) . " §fjoueur(s) suplementaire.");
                }
            }
        }
    }

}