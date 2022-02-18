<?php

namespace Zoumi\MiniGame\tasks\async;

use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use Zoumi\MiniGame\api\game\Bedwars;
use Zoumi\MiniGame\api\game\CaptureTheFlag;
use Zoumi\MiniGame\api\game\GunPlay;
use Zoumi\MiniGame\api\game\Hikabrain;
use Zoumi\MiniGame\api\WorldManager;
use Zoumi\MiniGame\Main;
use Zoumi\MiniGame\Manager;
use Zoumi\MiniGame\tasks\bedwars\WaitingRoomBW;
use Zoumi\MiniGame\tasks\ctf\WaitingRoomCTF;
use Zoumi\MiniGame\tasks\gunplay\WaitingRoomGP;
use Zoumi\MiniGame\tasks\hikabrain\WaitingRoomHB;
use Zoumi\MiniGame\utils\Functions;

class CopyWorld extends AsyncTask {

    /** @var $waitingRoom */
    private $waitingRoom;
    /** @var string $game */
    private $game;
    /** @var $typeGame */
    private $typeGame;

    public function __construct(string $waitingRoom, string $typeGame, string $game, $option = null)
    {
        $this->waitingRoom = $waitingRoom;
        $this->typeGame = $typeGame;
        $this->game = $game;
        $this->option = $option;
    }

    public function onRun()
    {
        Functions::copyWorld("spawn", $this->waitingRoom);
        Functions::copyWorld($this->typeGame, $this->game);
    }

    public function onCompletion(Server $server)
    {
        $server->loadLevel($this->waitingRoom);
        $wainting = $server->getLevelByName($this->waitingRoom);
        $wainting->setTime(9000);
        $wainting->stopTime();
        $server->loadLevel($this->game);
        $game = $server->getLevelByName($this->game);
        $game->setTime(9000);
        $game->stopTime();
        WorldManager::createWorld($wainting->getFolderName(), false, false, false, false, false, false, false);
        if ($this->typeGame === "GunPlay"){
            $gunplay = Main::$game["GunPlay"][$this->option];
            Main::getInstance()->getScheduler()->scheduleRepeatingTask(new WaitingRoomGP($gunplay), 20);
            WorldManager::createWorld($game->getFolderName(), false, false, false, false, true, false, true);
            foreach ($gunplay->getPlayersListArray() as $player){
                $p = Server::getInstance()->getPlayerExact($player);
                if ($p instanceof Player){
                    $p->teleport($wainting->getSafeSpawn());
                }
            }
        }
        if ($this->typeGame === "Hikabrain"){
            $hikabrain = Main::$game["Hikabrain"][$this->option];
            Main::getInstance()->getScheduler()->scheduleRepeatingTask(new WaitingRoomHB($hikabrain), 20);
            WorldManager::createWorld($game->getFolderName(), true, true, true, true, true, true, false);
            foreach ($hikabrain->getPlayersListArray() as $player){
                $p = Server::getInstance()->getPlayerExact($player);
                if ($p instanceof Player){
                    $p->teleport($wainting->getSafeSpawn());
                }
            }
        }
        if ($this->typeGame === "CaptureTheFlag"){
            Main::getInstance()->getScheduler()->scheduleRepeatingTask(new WaitingRoomCTF($this->game), 20);
            WorldManager::createWorld($game->getFolderName(), false, false, false, true, true, false, false);
            foreach (CaptureTheFlag::$ctf[$this->game]["players"] as $player => $points){
                $p = Server::getInstance()->getPlayerExact($player);
                if ($p instanceof Player){
                    $p->teleport($wainting->getSafeSpawn());
                }
            }
        }
        if ($this->typeGame === "Bedwars"){
            Main::getInstance()->getScheduler()->scheduleRepeatingTask(new WaitingRoomBW($this->game), 20);
            WorldManager::createWorld($game->getFolderName(), true, true, false, true, true, true, false);
            foreach (Bedwars::$bedwars[$this->game]["players"] as $player => $points){
                $p = Server::getInstance()->getPlayerExact($player);
                if ($p instanceof Player){
                    $p->teleport($wainting->getSafeSpawn());
                }
            }
        }
    }

}