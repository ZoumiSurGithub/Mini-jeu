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

class RoundTask extends Task {

    private $hikabrain;
    /** @var int $time */
    private $time = 5;

    public function __construct(HikabrainInstance $hikabrainInstance)
    {
        $this->hikabrain = $hikabrainInstance;
    }

    public function onRun(int $currentTick)
    {
        $hikabrain = $this->hikabrain;
        /* Timer */
        if (--$this->time === 0) {
            foreach ($hikabrain->getPlayersListArray() as $player) {
                $player = Server::getInstance()->getPlayerExact($player);
                if ($player instanceof Player) {
                    $player->setImmobile(false);
                    Hikabrain::sendStuff($player);
                    Functions::sendSound($player, "beacon.activate");
                    $player->addSubTitle("§l§aFight !");
                }
            }
            Main::getInstance()->getScheduler()->scheduleRepeatingTask(new HikabrainTask($hikabrain), 20);
            Main::getInstance()->getScheduler()->cancelTask($this->getTaskId());
            return;
        } elseif ($this->time <= 3) {
            foreach ($hikabrain->getPlayersListArray() as $player) {
                $p = Server::getInstance()->getPlayerExact($player);
                if ($p instanceof Player) {
                    $p->setImmobile(true);
                    $p->sendPopup(Manager::ANONCEUR . "Commence dans §c" . $this->time . " §fseconde(s).");
                }
            }
        } elseif ($this->time <= 5) {
            $red = explode(";", Manager::POS_HIKABRAIN["Rouge"]);
            $redcount = 0;
            $blue = explode(";", Manager::POS_HIKABRAIN["Bleu"]);
            $bluecount = 0;
            $red = explode(";", Manager::POS_HIKABRAIN["Rouge"]);
            $redcount = 0;
            $blue = explode(";", Manager::POS_HIKABRAIN["Bleu"]);
            $bluecount = 0;
            foreach ($hikabrain->getPlayersListByTeamArray("Rouge") as $player) {
                $p = Server::getInstance()->getPlayerExact($player);
                if ($p instanceof Player) {
                    $pos = explode(":", $red[$redcount]);
                    $p->teleport(new Position($pos[0], $pos[1], $pos[2], Server::getInstance()->getLevelByName($hikabrain->getId())));
                    $p->setImmobile(true);
                    $p->sendPopup(Manager::ANONCEUR . "Commence dans §6" . $this->time . " §fseconde(s).");
                    $redcount++;
                }
            }
            foreach ($hikabrain->getPlayersListByTeamArray("Bleu") as $player) {
                $p = Server::getInstance()->getPlayerExact($player);
                if ($p instanceof Player) {
                    $pos = explode(":", $blue[$bluecount]);
                    $p->teleport(new Position($pos[0], $pos[1], $pos[2], Server::getInstance()->getLevelByName($hikabrain->getId())));
                    $p->setImmobile(true);
                    $p->sendPopup(Manager::ANONCEUR . "Commence dans §6" . $this->time . " §fseconde(s).");
                    $bluecount++;
                }
            }
        }
    }

}