<?php

namespace Zoumi\MiniGame\api\game;

use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\Server;
use Zoumi\MiniGame\api\Webhook;
use Zoumi\MiniGame\Main;
use Zoumi\MiniGame\Manager;
use Zoumi\MiniGame\tasks\async\CopyWorld;
use Zoumi\MiniGame\utils\Functions;

class GunPlay extends GameManager{

    public static $count = 0;

    /** @var array $scoreboard */
    public static $scoreboard = [];

    private $waitingRoom;

    private $maxPoints = 260;

    public function __construct(Player $player, string $type, bool $private)
    {
        $player->sendMessage(Manager::CREATE_PARTIES);
        /* Base */
        $count = self::$count = self::$count + 1;
        $this->id = "GunPlay-{$type}-{$count}";
        /* Joueur maximum */
        $maxPlayers = explode("v", $type);
        $maxPlayers = $maxPlayers[1];
        $maxPlayers = intval($maxPlayers);
        /* Initialisation des données */
        $this->setGameType("GunPlay");
        $this->setHoster($player);
        $this->setPlayersCount(1);
        $this->setMaxPlayersCount($maxPlayers);
        $this->setMinPlayersCount($maxPlayers / 2);
        $this->setStatus("§6en attente");
        $this->setType($type);
        $this->setPrivate($private);
        if ($private){
            $this->setCode($this->genCode());
            $player->sendMessage(Manager::VIEW_CODE);
        }
        /* Joueur */
        $this->addPlayer($player, 0);
        /* Création des mondes */
        $countRoom = Main::$waitingRoomCount = Main::$waitingRoomCount + 1;
        $this->waitingRoom = "WaitingRoom$countRoom";
        /* Logs */
        Webhook::sendGameLogs("> Un jeu d'arme vient d'être créer ayant pour id **GunPlay-{$type}-{$count}** __**{$type}**__.");
        Server::getInstance()->getAsyncPool()->submitTask(new CopyWorld($this->waitingRoom, "GunPlay", $this->id, $player->getName()));
    }

    public function getId(): string{
        return $this->id;
    }

    public function getWaitingRoom(): string{
        return $this->waitingRoom;
    }

    public function addNewPlayer(Player $player){
        $this->setPlayersCount($this->getPlayersCount() + 1);
        $this->addPlayer($player, 0);
    }

    public function removeCurrentPlayer(Player $player){
        $this->setPlayersCount($this->getPlayersCount() - 1);
        $this->removePlayer($player);
    }

    public function getPointOfPlayer(Player $player){
        return $this->players[$player->getName()];
    }

    public function addPointOfPlayer(Player $player, int $points = 10){
        $this->players[$player->getName()] = $this->getPointOfPlayer($player) + $points;
    }

    public function sendWeapon(Player $player, int $point){
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        if ($point === 0){
            $player->getInventory()->setItem(0, Item::get(Item::DIAMOND_SWORD));
        }elseif($point === 10){
            $player->getInventory()->setItem(0, Item::get(Item::DIAMOND_AXE));
        }elseif ($point === 20){
            $player->getInventory()->setItem(0, Item::get(Item::DIAMOND_PICKAXE));
        }elseif ($point === 30){
            $player->getInventory()->setItem(0, Item::get(Item::DIAMOND_SHOVEL));
        }elseif ($point === 40){
            $player->getInventory()->setItem(0, Item::get(Item::DIAMOND_HOE));
        }elseif ($point === 50){
            $player->getInventory()->setItem(0, Item::get(Item::GOLD_SWORD));
        }elseif ($point === 60){
            $player->getInventory()->setItem(0, Item::get(Item::GOLD_AXE));
        }elseif ($point === 70){
            $player->getInventory()->setItem(0, Item::get(Item::GOLD_PICKAXE));
        }elseif ($point === 80){
            $player->getInventory()->setItem(0, Item::get(Item::GOLD_SHOVEL));
        }elseif ($point === 90){
            $player->getInventory()->setItem(0, Item::get(Item::GOLD_HOE));
        }elseif ($point === 100){
            $player->getInventory()->setItem(0, Item::get(Item::IRON_SWORD));
        }elseif ($point === 110){
            $player->getInventory()->setItem(0, Item::get(Item::IRON_AXE));
        }elseif ($point === 120){
            $player->getInventory()->setItem(0, Item::get(Item::IRON_PICKAXE));
        }elseif ($point === 130){
            $player->getInventory()->setItem(0, Item::get(Item::IRON_SHOVEL));
        }elseif ($point === 140){
            $player->getInventory()->setItem(0, Item::get(Item::IRON_HOE));
        }elseif ($point === 150){
            $player->getInventory()->setItem(0, Item::get(Item::STONE_SWORD));
        }elseif ($point === 160){
            $player->getInventory()->setItem(0, Item::get(Item::STONE_AXE));
        }elseif ($point === 170){
            $player->getInventory()->setItem(0, Item::get(Item::STONE_PICKAXE));
        }elseif ($point === 180){
            $player->getInventory()->setItem(0, Item::get(Item::STONE_SHOVEL));
        }elseif ($point === 190){
            $player->getInventory()->setItem(0, Item::get(Item::STONE_HOE));
        }elseif ($point === 200){
            $player->getInventory()->setItem(0, Item::get(Item::WOODEN_SWORD));
        }elseif ($point === 210){
            $player->getInventory()->setItem(0, Item::get(Item::WOODEN_AXE));
        }elseif ($point === 220){
            $player->getInventory()->setItem(0, Item::get(Item::WOODEN_PICKAXE));
        }elseif ($point === 230){
            $player->getInventory()->setItem(0, Item::get(Item::WOODEN_SHOVEL));
        }elseif ($point === 240){
            $player->getInventory()->setItem(0, Item::get(Item::WOODEN_HOE));
        }elseif ($point === 250){
            $player->getInventory()->clearAll();
        }
    }

    public function getTopOne(): string
    {
        $p = 0;
        $pla = "";
        $i = 0;
        foreach ($this->players as $player => $points) {
            if ($p <= $points) {
                $p = $points;
                $pla = $player;
            }
        }
        return $pla . " §7($p)";
    }

    public function stop(string $reason){
        $idLevel = Server::getInstance()->getLevelByName($this->getId());
        $waitingLevel = Server::getInstance()->getLevelByName($this->getWaitingRoom());
        foreach ($idLevel->getPlayers() as $player){
            Functions::goToSpawn($player);
        }
        $idLevel->unload(true);
        foreach ($waitingLevel->getPlayers() as $player){
            Functions::goToSpawn($player);
        }
        foreach ($this->players as $player => $value){
            $p = Server::getInstance()->getPlayerExact($player);
            if ($p instanceof SPlayer){
                $p->setGame(null);
            }
        }
        $waitingLevel->unload(true);
        Functions::removeDir(Server::getInstance()->getDataPath() . "/worlds/" . $this->getId());
        Functions::removeDir(Server::getInstance()->getDataPath() . "/worlds/" . $this->getWaitingRoom());
        $this->broadcastMessage(Manager::ANONCEUR . "La partie est terminer. Raison: §7{$reason}§f.");
        Webhook::sendGameLogs("> Le jeu d'arme ayant pour id **{$this->getId()}** vient d'être stopper pour la raison **{$reason}**.");
        unset(Main::$game["GunPlay"][$this->getHoster()->getName()]);
    }

}