<?php

namespace Zoumi\MiniGame\tasks\bedwars;

use pocketmine\level\generator\GeneratorRegisterTask;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use Zoumi\MiniGame\api\game\Bedwars;
use Zoumi\MiniGame\api\game\TeamManager;
use Zoumi\MiniGame\blocks\Bed;
use Zoumi\MiniGame\Main;
use Zoumi\MiniGame\Manager;
use Zoumi\MiniGame\utils\Functions;

class WaitingRoomBW extends Task {

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
        if (isset(Bedwars::$bedwars[$id])) {
            if (Bedwars::getPlayersCount($id) >= 1) {
                if (--$this->time === 0) {
                    /* Emerald */
                    $pos = explode(";", Bedwars::$genPos["Emerald"]);
                    foreach ($pos as $po) {
                        $pos = explode(":", $po);
                        $nbt = \pocketmine\entity\Entity::createBaseNBT(new Position((float)$pos[0], (int)$pos[1], (float)$pos[2], Server::getInstance()->getLevelByName($id)));
                        $entity = \pocketmine\entity\Entity::createEntity("Emerald", Server::getInstance()->getLevelByName($id), $nbt);
                        $entity->spawnToAll();
                    }
                    /* Diamond */
                    $pos = explode(";", Bedwars::$genPos["Diamond"]);
                    foreach ($pos as $po) {
                        $pos = explode(":", $po);
                        $nbt = \pocketmine\entity\Entity::createBaseNBT(new Position((float)$pos[0], (int)$pos[1], (float)$pos[2], Server::getInstance()->getLevelByName($id)));
                        $entity = \pocketmine\entity\Entity::createEntity("Diamond", Server::getInstance()->getLevelByName($id), $nbt);
                        $entity->spawnToAll();
                    }
                    /* Iron */
                    $pos = explode(";", Bedwars::$genPos["Iron"]);
                    foreach ($pos as $po) {
                        $pos = explode(":", $po);
                        $nbt = \pocketmine\entity\Entity::createBaseNBT(new Position((float)$pos[0], (int)$pos[1], (float)$pos[2], Server::getInstance()->getLevelByName($id)));
                        $entity = \pocketmine\entity\Entity::createEntity("Iron", Server::getInstance()->getLevelByName($id), $nbt);
                        $entity->spawnToAll();
                    }
                    foreach (Bedwars::$bedwars[$id]["players"] as $player) {
                        $p = Server::getInstance()->getPlayerExact($player);
                        if ($p instanceof Player) {
                            $pos = explode(":", Bedwars::$pos[TeamManager::getTeamOfPlayer($id, $p)]["pnj"]);
                            $nbt = \pocketmine\entity\Entity::createBaseNBT(new Position((float)$pos[0], (int)$pos[1], (float)$pos[2], Server::getInstance()->getLevelByName($id)), null);
                            $entity = \pocketmine\entity\Entity::createEntity("VillagerBW", Server::getInstance()->getLevelByName($id), $nbt);
                            $entity->spawnToAll();
                            $p->setImmobile(false);
                            Functions::sendSound($p, "beacon.activate");
                            $p->sendMessage(
                                "\n\n§c§lJOUEZ EN TEAM EST INTERDIT\n\n" .
                                "§l§e» §fVous êtes dans l'équipe " . TeamManager::getTeamOfPlayer($id, $p, true) . "§f.\n\n"
                            );
                        }
                    }
                    Bedwars::$bedwars[$id]["status"] = "§aen cours";
                    Bedwars::$bedwars[$id]["time"] = ["time" => time(), "left" => time() + 60 * 30, "bedExplose" => time() + 60 * 25];
                    Main::getInstance()->getScheduler()->scheduleRepeatingTask(new BedwarsTask($id), 20);
                    Main::getInstance()->getScheduler()->scheduleRepeatingTask(new GeneratorTask($id), 20);
                    Main::getInstance()->getScheduler()->cancelTask($this->getTaskId());
                    return;
                } elseif ($this->time === 10) {
                    foreach (Bedwars::$bedwars[$id]["players"] as $player) {
                        $p = Server::getInstance()->getPlayerExact($player);
                        if ($p instanceof Player) {
                            if (!TeamManager::isInTeam($id, $p)){
                                TeamManager::setRandomTeamForNewPlayer($id, $p);
                            }
                            $pos = explode(":", Bedwars::$pos[TeamManager::getTeamOfPlayer($id, $p)]["spawn"]);
                            $p->teleport(new Position($pos[0], $pos[1], $pos[2], Server::getInstance()->getLevelByName($id)));
                        }
                    }
                    foreach (Bedwars::$bedwars[$id]["players"] as $player) {
                        $p = Server::getInstance()->getPlayerExact($player);
                        if ($p instanceof Player) {
                            $p->setImmobile(true);
                            Functions::sendSound($p, "note.harp");
                            $p->sendPopup(Manager::ANONCEUR . "Commence dans §a" . $this->time . " §fseconde(s).");
                        }
                    }
                }
                if ($this->time <= 3) {
                    foreach (Bedwars::$bedwars[$id]["players"] as $player => $pla) {
                        $p = Server::getInstance()->getPlayerExact($player);
                        if ($p instanceof Player) {
                            $p->setImmobile(true);
                            Functions::sendSound($p, "note.bit");
                            $p->sendPopup(Manager::ANONCEUR . "Commence dans §c" . $this->time . " §fseconde(s).");
                        }
                    }
                }elseif ($this->time <= 5) {
                    foreach (Bedwars::$bedwars[$id]["players"] as $player => $pla) {
                        $p = Server::getInstance()->getPlayerExact($player);
                        if ($p instanceof Player) {
                            $p->setImmobile(true);
                            Functions::sendSound($p, "note.xylophone");
                            $p->sendPopup(Manager::ANONCEUR . "Commence dans §6" . $this->time . " §fseconde(s).");
                        }
                    }
                }elseif ($this->time <= 10) {
                    foreach (Bedwars::$bedwars[$id]["players"] as $player => $pla) {
                        $p = Server::getInstance()->getPlayerExact($player);
                        if ($p instanceof Player) {
                            $p->setImmobile(true);
                            Functions::sendSound($p, "note.harp");
                            $p->sendPopup(Manager::ANONCEUR . "Commence dans §a" . $this->time . " §fseconde(s).");
                        }
                    }
                }
            } else {
                foreach (Bedwars::$bedwars[$id]["players"] as $player => $team) {
                    $p = Server::getInstance()->getPlayerExact($player);
                    if ($p instanceof Player) {
                        $p->sendPopup(Manager::ANONCEUR . "En attente de plus de joueur.");
                    }
                }
            }
        }else Main::getInstance()->getScheduler()->cancelTask($this->getTaskId());
    }

}