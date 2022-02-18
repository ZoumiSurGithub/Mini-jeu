<?php

namespace Zoumi\MiniGame\tasks\ctf;

use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use Zoumi\MiniGame\api\game\CaptureTheFlag;
use Zoumi\MiniGame\api\game\TeamManager;
use Zoumi\MiniGame\Main;
use Zoumi\MiniGame\Manager;
use Zoumi\MiniGame\utils\Functions;

class WaitingRoomCTF extends Task {

    /** @var string $id */
    public $id;
    /** @var int $time */
    public $time = 11;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function onRun(int $currentTick)
    {
        $id = $this->id;
        if (isset(CaptureTheFlag::$ctf[$id])) {
            if (CaptureTheFlag::getPlayersCount($id) >= 6) {
                if (--$this->time === 0) {
                    $nbt = \pocketmine\entity\Entity::createBaseNBT(new Position(-589, 35, -302.1, Server::getInstance()->getLevelByName($id)));
                    $entity = \pocketmine\entity\Entity::createEntity("FlagRed", Server::getInstance()->getLevelByName($id), $nbt);
                    $entity->spawnToAll();
                    $nbt = \pocketmine\entity\Entity::createBaseNBT(new Position(-465.5, 36, -479.5, Server::getInstance()->getLevelByName($id)));
                    $entity = \pocketmine\entity\Entity::createEntity("FlagBlue", Server::getInstance()->getLevelByName($id), $nbt);
                    $entity->spawnToAll();
                    foreach (CaptureTheFlag::$ctf[$id]["players"] as $player) {
                        $p = Server::getInstance()->getPlayerExact($player);
                        if ($p instanceof Player) {
                            CaptureTheFlag::sendStuff($p);
                            $p->setImmobile(false);
                            $p->addSubTitle("§l§aFight !");
                        }
                    }
                    CaptureTheFlag::$ctf[$id]["status"] = "§aen cours";
                    CaptureTheFlag::$ctf[$id]["time"] = ["time" => time(), "left" => time() + 60 * 10];
                    Main::getInstance()->getScheduler()->scheduleRepeatingTask(new CaptureTheFlagTask($id), 20);
                    Main::getInstance()->getScheduler()->cancelTask($this->getTaskId());
                    return;
                } elseif ($this->time === 10) {
                    foreach (CaptureTheFlag::$ctf[$id]["players"] as $player) {
                        $p = Server::getInstance()->getPlayerExact($player);
                        if ($p instanceof Player) {
                            if (!TeamManager::isInTeam($id, $p)){
                                TeamManager::setRandomTeamForNewPlayer($id, $p);
                            }
                            $p->setImmobile(true);
                            Functions::sendSound($p, "note.chime");
                            $p->sendPopup(Manager::ANONCEUR . "Commence dans §a" . Main::getInstance()->convert($this->time));
                        }
                    }
                    $red = explode(";", CaptureTheFlag::$pos["Rouge"]);
                    $redcount = 0;
                    $blue = explode(";", CaptureTheFlag::$pos["Bleu"]);
                    $bluecount = 0;
                    foreach (TeamManager::$team[$id]["Rouge"]["players"] as $player) {
                        $p = Server::getInstance()->getPlayerExact($player);
                        if ($p instanceof Player) {
                            $pos = explode(":", $red[$redcount]);
                            $p->teleport(new Position($pos[0], $pos[1], $pos[2], Server::getInstance()->getLevelByName($id)));
                            $redcount++;
                        }
                    }
                    foreach (TeamManager::$team[$id]["Bleu"]["players"] as $player){
                        $p = Server::getInstance()->getPlayerExact($player);
                        if ($p instanceof Player) {
                            $pos = explode(":", $blue[$bluecount]);
                            $p->teleport(new Position($pos[0], $pos[1], $pos[2], Server::getInstance()->getLevelByName($id)));
                            $bluecount++;
                        }
                    }
                } elseif ($this->time <= 3) {
                    foreach (CaptureTheFlag::$ctf[$id]["players"] as $player) {
                        $p = Server::getInstance()->getPlayerExact($player);
                        if ($p instanceof Player) {
                            $p->setImmobile(true);
                            Functions::sendSound($p, "note.bell");
                            $p->sendPopup(Manager::ANONCEUR . "Commence dans §c" . Main::getInstance()->convert($this->time));
                        }
                    }
                } elseif ($this->time <= 5) {
                    foreach (CaptureTheFlag::$ctf[$id]["players"] as $player) {
                        $p = Server::getInstance()->getPlayerExact($player);
                        if ($p instanceof Player) {
                            $p->setImmobile(true);
                            Functions::sendSound($p, "note.bell");
                            $p->sendPopup(Manager::ANONCEUR . "Commence dans §6" . Main::getInstance()->convert($this->time));
                        }
                    }
                } elseif ($this->time <= 9) {
                    foreach (CaptureTheFlag::$ctf[$id]["players"] as $player) {
                        $p = Server::getInstance()->getPlayerExact($player);
                        if ($p instanceof Player) {
                            $p->setImmobile(true);
                            Functions::sendSound($p, "note.chime");
                            $p->sendPopup(Manager::ANONCEUR . "Commence dans §a" . Main::getInstance()->convert($this->time));
                        }
                    }
                }
            } else {
                foreach (CaptureTheFlag::$ctf[$id]["players"] as $player => $team) {
                    $p = Server::getInstance()->getPlayerExact($player);
                    if ($p instanceof Player) {
                        $p->sendPopup(Manager::ANONCEUR . "En attente de §c" . (CaptureTheFlag::getMaxPlayersCount($id) - CaptureTheFlag::getPlayersCount($id)) . " §fjoueur(s) suplémentaire.");
                    }
                }
            }
        }else Main::getInstance()->getScheduler()->cancelTask($this->getTaskId());
    }

}