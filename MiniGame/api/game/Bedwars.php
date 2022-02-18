<?php

namespace Zoumi\MiniGame\api\game;

use jojoe77777\FormAPI\SimpleForm;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\Server;
use Zoumi\MiniGame\Main;
use Zoumi\MiniGame\Manager;
use Zoumi\MiniGame\tasks\async\CopyWorld;
use Zoumi\MiniGame\utils\Functions;

class Bedwars {

    /** @var array $bedwars */
    public static $bedwars = [];
    /** @var array $players */
    public static $players = [];
    /** @var array $scoreboard */
    public static $scoreboard = [];
    /** @var array $spectator */
    public static $spectator = [];
    /** @var int $count */
    public static $count = 0;
    /** @var \string[][] $pos */
    public static $pos = [
        "Rouge" => [
            "spawn" => "-478.5:43:-283.5",
            "pnj" => "-482.5:43:-283.5",
            "gen" => "-480.5:45:-286.5"
        ],
        "Bleu" => [
            "spawn" => "-534.5:43:-345.5",
            "pnj" => "-534.5:43:-349.5",
            "gen" => "-531.5:45:-347.5"
        ],
        "Jaune" => [
            "spawn" => "-472.5:43:-401.5",
            "pnj" => "-416.5:43:-335.5",
            "gen" => "-419.5:45:-337.5"
        ],
        "Vert" => [
            "spawn" => "-416.5:43:-339.5",
            "pnj" => "-468.5:43:-401.5",
            "gen" => "-470.5:45:-398.5"
        ]
    ];
    public static $genPos = array(
        "Emerald" => "-490.5:43:-342.5;-475.5:43:-357.5;-460.5:43:-342.5;-475.5:43:-327.5",
        "Diamond" => "-419.5:46:-398.5;-531.5:46:-398.5;-531.5:46:-286.5;-419.5:46:-286.5",
        "Iron" => "-480.5:45:-286.5;-531.5:45:-347.5;-419.5:45:-337.5;-470.5:45:-398.5"
    );
    public static $entityId = [];

    public static function createBedwars(Player $player, bool $private = false){
        $count = self::$count = self::$count + 1;
        $countRoom = Main::$waitingRoomCount = Main::$waitingRoomCount + 1;
        $code = Functions::genCode(10);
        self::$bedwars["Bedwars$count"] = [
            "players" => [$player->getName() => $player->getName()],
            "maxPlayers" => 4,
            "minPlayers" => 2,
            "playersCount" => 1,
            "waitingRoom" => "WaitingRoomGP$countRoom",
            "status" => "§6en attente",
            "isPrivate" => $private,
            "code" => $code,
            "name" => "Bedwars$count"
        ];
        TeamManager::$team["Bedwars$count"] = [
            "Rouge" => [
                "players" => [],
                "bedAlive" => true,
                "maxPlayers" => 1
            ],
            "Bleu" => [
                "players" => [],
                "bedAlive" => true,
                "maxPlayers" => 1
            ],
            "Jaune" => [
                "players" => [],
                "bedAlive" => true,
                "maxPlayers" => 1
            ],
            "Vert" => [
                "players" => [],
                "bedAlive" => true,
                "maxPlayers" => 1
            ],
            "maxTeam" => 4
        ];
        self::$players[$player->getName()] = "Bedwars$count";
        self::$entityId["Bedwars$count"] = [];
        Webhook::sendGameLogs("> Une partie de bedwars vient d'être lancer par **" . $player->getName() . "**, l'id de la partie est **Bedwars{$count}**.");
        $player->sendMessage(Manager::CREATE_PARTIES);
        NavigatorManager::sendWool($player);
        if ($private){
            PrivateGameManager::$private[$player->getName()] = [
                "game" => "Bedwars$count",
                "type" => "Bedwars",
                "code" => $code
            ];
            $player->sendMessage(Manager::VIEW_CODE);
        }
        Server::getInstance()->getAsyncPool()->submitTask(new CopyWorld("WaitingRoomGP$countRoom", "Bedwars", "Bedwars$count"));
    }

    public static function getPlayersCount(string $id): int{
        return self::$bedwars[$id]["playersCount"];
    }

    public static function getMaxPlayersCount(string $id): int{
        return self::$bedwars[$id]["maxPlayers"];
    }

    public static function stopBedwars(string $id, string $reason){
        foreach (self::$entityId[$id] as $count => $id){
            Server::getInstance()->getLevelByName($id)->getEntity($id)->flagForDespawn();
        }
        unset(self::$entityId[$id]);
        foreach (Server::getInstance()->getLevelByName(self::$bedwars[$id]["waitingRoom"])->getPlayers() as $player){
            $player->sendMessage(Manager::ANONCEUR . "Téléportation au spawn.");
            Functions::goToSpawn($player);
        }
        Server::getInstance()->getLevelByName(self::$bedwars[$id]["waitingRoom"])->unload(true);
        Functions::removeDir(Server::getInstance()->getDataPath() . "/worlds/" . self::$bedwars[$id]["waitingRoom"]);
        foreach (Server::getInstance()->getLevelByName(self::$bedwars[$id]["name"])->getPlayers() as $player){
            $player->sendMessage(Manager::ANONCEUR . "Téléportation au spawn.");
            Functions::goToSpawn($player);
        }
        Server::getInstance()->getLevelByName(self::$bedwars[$id]["name"])->unload(true);
        Functions::removeDir(Server::getInstance()->getDataPath() . "/worlds/" . self::$bedwars[$id]["name"]);
        foreach (self::$bedwars[$id]["players"] as $player => $points){
            $p = Server::getInstance()->getPlayerExact($player);
            if ($p instanceof Player){
                $p->sendMessage(Manager::ANONCEUR . "La partie est terminer. Raison: §7{$reason}§f.");
            }
        }
        foreach (self::$scoreboard as $player => $value){
            unset(self::$scoreboard[$player]);
        }
        foreach (self::$players as $player => $bedwars){
            if ($bedwars === $id){
                unset(self::$players[$player]);
            }
        }
        foreach (PrivateGameManager::$private as $player => $value){
            if (PrivateGameManager::$private[$player]["game"] === $id){
                unset(PrivateGameManager::$private[$player]);
            }
        }
        Webhook::sendGameLogs("> Le bedwars ayant pour id **" . $id . "** vient d'être stopper pour la raison **$reason**.");
        unset(self::$bedwars[$id]);
    }

    public static function setSpectator(string $id, Player $player){
        if (isset(self::$bedwars[$id])){
            if (isset(self::$players[$player->getName()])){
                TeamManager::removePlayerInTeam(self::$players[$player->getName()], $player, TeamManager::getTeamOfPlayer(self::$players[$player->getName()], $player));
            }
        }
    }

    public static function getGameInWaiting(): int{
        $count = 0;
        foreach (self::$bedwars as $id => $value){
            if (self::$bedwars[$id]["status"] === "§6en attente"){
                $count++;
            }
        }
        return $count;
    }

    public static function getPartyInPlaying(): int{
        $count = 0;
        foreach (self::$bedwars as $id => $value){
            if (self::$bedwars[$id]["status"] === "§aen cours"){
                $count++;
            }
        }
        return $count;
    }

    /* UI */
    public static function sendShopMenu(Player $player){
        $ui = new SimpleForm(function (Player $player, $data){
            if ($data === null){
                return;
            }
            switch ($data){

            }
        });
        $ui->setTitle("§6- §fShop §6-");
        $ui->addButton("Blocs");
        $ui->addButton("Armures");
        $ui->addButton("Outils");
        $ui->sendToPlayer($player);
    }

    public static function sendBlockShop(Player $player){
        $ui = new SimpleForm(function (Player $player, $data){
            if ($data === null){
                return;
            }
            switch ($data){

            }
        });
        $ui->setTitle("§6- §fShop §6-");
        if (self::getCountOfItem($player, Item::get(Item::IRON_INGOT)) >= 5){
            $ui->addButton("Laine x30\n§a5 lingots de fer");
        }else{
            $ui->addButton("Laine x30\n§c5 lingots de fer");
        }
        if (self::getCountOfItem($player, Item::get(Item::IRON_INGOT)) >= 15){
            $ui->addButton("Planche x30\n§a15 lingots de fer");
        }else{
            $ui->addButton("Planche x15\n§c15 lingots de fer");
        }
        $ui->addButton("");
    }

    public static function getCountOfItem(Player $player, Item $item): int{
        $count = 0;
        foreach ($player->getInventory()->getContents() as $slot => $itm){
            if ($item->getId() === $itm->getId() && $item->getDamage() === $itm->getDamage()){
                $count += $itm->getCount();
            }
        }
        return $count;
    }
    
}