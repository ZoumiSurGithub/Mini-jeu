<?php

namespace Zoumi\MiniGame\api\game;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\Server;
use Zoumi\MiniGame\commands\all\Navigator;
use Zoumi\MiniGame\Main;
use Zoumi\MiniGame\Manager;
use Zoumi\MiniGame\tasks\async\CopyWorld;
use Zoumi\MiniGame\utils\Functions;

class Hikabrain {

    /** @var array $hikabrain */
    public static $hikabrain = [];
    /** @var array $players */
    public static $players = [];
    /** @var array $scoreboard */
    public static $scoreboard = [];
    /** @var array $private */
    public static $private = [];
    /** @var int $count */
    public static $count = 0;
    /** @var array $team */
    public static $team = array(
        0 => "Rouge",
        1 => "Bleu"
    );
    /** @var array $pos */
    public static $pos = array(
        "Rouge" => "-501.5:10:-351.5;-501.5:10:-352.5;-501.5:10:-350.5;-500.5:10:-351.5",
        "Bleu" => "-546.5:10:-351.5;-546.5:10:-350.5;-546.5:10:-352.5;-547.5:10:-351.5"
    );

    public static function createHikabrain(Player $player, string $type, bool $private)
    {
        $count = self::$count = self::$count + 1;
        $countRoom = Main::$waitingRoomCount = Main::$waitingRoomCount + 1;
        $maxPlayers = explode("v", $type);
        $maxPlayers = $maxPlayers[0] + $maxPlayers[1];
        $minPlayers = 0;
        if ($type === "1v1") {
            $minPlayers = 2;
        } else {
            $minPlayers = $maxPlayers / 2;
        }
        $code = Functions::genCode(10);
        self::$hikabrain["HikaBrain{$type}-{$count}"] = [
            "players" => [$player->getName() => $player->getName()],
            "maxPlayers" => $maxPlayers,
            "minPlayers" => $minPlayers,
            "playersCount" => 1,
            "waitingRoom" => "WaitingRoomGP$countRoom",
            "name" => "HikaBrain{$type}-{$count}",
            "status" => "§6en attente",
            "type" => $type,
            "isPrivate" => $private,
            "code" => $code
        ];
        TeamManager::$team["HikaBrain{$type}-{$count}"] = [
            "Rouge" => [
                "players" => [],
                "maxPlayers" => $minPlayers,
                "points" => 0
            ],
            "Bleu" => [
                "players" => [],
                "maxPlayers" => $minPlayers,
                "points" => 0
            ],
            "maxTeam" => 2
        ];
        self::$players[$player->getName()] = "HikaBrain{$type}-{$count}";
        Webhook::sendGameLogs("> Un hikabrain vient d'être lancer par **" . $player->getName() . "**, ayant pour id **HikaBrain{$type}-{$count}**.");
        NavigatorManager::sendNavigatorForSelectTeam($player);
        $player->sendMessage(Manager::ANONCEUR . "Création de la partie en cours... Téléportation dans la salle d'attente.");
        if ($private) {
            PrivateGameManager::$private[$player->getName()] = [
                "game" => "HikaBrain{$type}-{$count}",
                "code" => $code,
                "type" => "Hikabrain"
            ];
            $player->sendMessage(Manager::PREFIX_INFOS . "Pour voir le code de votre partie privée faites §7/code§f.");
        }
        Server::getInstance()->getAsyncPool()->submitTask(new CopyWorld("WaitingRoomGP$countRoom", "Hikabrain", "HikaBrain{$type}-{$count}"));
    }

    public static function addPlayer(string $id, Player $player)
    {
        if (isset(self::$hikabrain[$id])) {
            self::$hikabrain[$id]["players"][$player->getName()] = $player->getName();
            self::$hikabrain[$id]["playersCount"] = self::$hikabrain[$id]["playersCount"] + 1;
            self::$players[$player->getName()] = $id;
            foreach (Server::getInstance()->getLevelByName(self::$hikabrain[$id]["name"])->getPlayers() as $p) {
                if ($p instanceof Player) {
                    $p->sendMessage("§d" . $player->getName() . " §fvient de rejoindre la partie. §7(" . self::getPlayersCount($id) . "/" . self::getPlayersMaxCount($id) . ")");
                }
            }
            foreach (Server::getInstance()->getLevelByName(self::$hikabrain[$id]["waitingRoom"])->getPlayers() as $p) {
                if ($p instanceof Player) {
                    $p->sendMessage("§d" . $player->getName() . " §fvient de rejoindre la partie. §7(" . self::getPlayersCount($id) . "/" . self::getPlayersMaxCount($id) . ")");
                }
            }
            if (self::$hikabrain[$id]["status"] === "§6en attente") {
                if (self::$hikabrain[$id]["type"] !== "1v1"){
                    NavigatorManager::sendWool($player);
                }
                $player->sendMessage(Manager::ANONCEUR . "Téléportation vers la salle d'attente.");
                $player->teleport(Server::getInstance()->getLevelByName(self::$hikabrain[$id]["waitingRoom"])->getSafeSpawn());
            } elseif (self::$hikabrain[$id]["status"] === "§aen cours") {
                TeamManager::setRandomTeamForNewPlayer($id, $player);
                $pos = explode(":", self::$pos[TeamManager::getTeamOfPlayer($id, $player)]);
                $pos = explode(":", $pos[0]);
                $player->teleport(new Position($pos[0], $pos[1], $pos[2], Server::getInstance()->getLevelByName(self::$hikabrain[$id]["name"])));
                self::sendStuff($player);
                $player->addTitle("§l§aCombattez !");
            }
        }
    }

    public static function removePlayer(string $id, Player $player){
        if (isset(self::$hikabrain[$id])){
            unset(self::$hikabrain[$id]["players"][$player->getName()]);
            unset(self::$players[$player->getName()]);
            self::$hikabrain[$id]["playersCount"] = self::getPlayersCount($id) - 1;
            if (TeamManager::isInTeam($id, $player)) {
                TeamManager::removePlayerInTeam($id, $player, TeamManager::getTeamOfPlayer($id, $player));
            }
            foreach (Server::getInstance()->getLevelByName(self::$hikabrain[$id]["name"])->getPlayers() as $p){
                if ($p instanceof Player){
                    $p->sendMessage("§d" . $player->getName() . " §fvient de quitter la partie. §7(" . self::getPlayersCount($id) . "/" . self::getPlayersMaxCount($id) . ")");
                }
            }
            foreach (Server::getInstance()->getLevelByName(self::$hikabrain[$id]["waitingRoom"])->getPlayers() as $p){
                if ($p instanceof Player){
                    $p->sendMessage("§d" . $player->getName() . " §fvient de quitter la partie. §7(" . self::getPlayersCount($id) . "/" . self::getPlayersMaxCount($id) . ")");
                }
            }
            if (self::$hikabrain[$id]["playersCount"] < 1){
                self::stopHikabrain($id, "aucun joueur dans cette partie");
            }else{
                Functions::goToSpawn($player);
            }
        }
    }
    
    public static function getGameInWaiting(): int{
        $count = 0;
        foreach (self::$hikabrain as $id => $value){
            if (self::$hikabrain[$id]["status"] === "§6en attente"){
                $count++;
            }
        }
        return $count;
    }

    public static function getPartyInPlaying(): int{
        $count = 0;
        foreach (self::$hikabrain as $id => $value){
            if (self::$hikabrain[$id]["status"] === "§aen cours"){
                $count++;
            }
        }
        return $count;
    }

    public static function getPlayersCount(string $id): int{
        if (isset(self::$hikabrain[$id])){
            return self::$hikabrain[$id]["playersCount"];
        }
        return 0;
    }

    public static function getPlayersMaxCount(string $id): int{
        if (isset(self::$hikabrain[$id])){
            return self::$hikabrain[$id]["maxPlayers"];
        }
        return 0;
    }

    public static function getMinPlayersCount(string $id): int{
        if (isset(self::$hikabrain[$id])){
            return self::$hikabrain[$id]["minPlayers"];
        }
        return 0;
    }

    public static function getPointForWin(string $id): int{
        if (isset(self::$hikabrain[$id])){
            if (TeamManager::getPoint($id, "Rouge") === 2 && TeamManager::getPoint($id, "Bleu") === 0){
                return 3;
            }elseif (TeamManager::getPoint($id, "Bleu") === 2 && TeamManager::getPoint($id, "Rouge") === 0){
                return 3;
            }elseif (TeamManager::getPoint($id, "Rouge")  === 2 && TeamManager::getPoint($id, "Bleu") === TeamManager::getPoint($id, "Rouge")  - 1){
                return TeamManager::getPoint($id, "Rouge") + 2;
            }elseif (TeamManager::getPoint($id, "Bleu") === 2 && TeamManager::getPoint($id, "Rouge") === TeamManager::getPoint($id, "Bleu") - 1){
                return TeamManager::getPoint($id, "Bleu") + 2;
            }
        }
        return 3;
    }

    public static function getPointFor(string $id, string $team){
        if (isset(self::$hikabrain[$id])){
            return self::$hikabrain[$id][$team];
        }
    }

    public static function addPoint(string $id, string $team, int $point = 1){
        if (isset(self::$hikabrain[$id])){
            $actus = self::$hikabrain[$id][$team];
            self::$hikabrain[$id][$team] = $actus + $point;
        }
    }

    public static function getPlayersList($id): string{
        if (isset(self::$hikabrain[$id])) {
            $players = [];
            foreach (self::$hikabrain[$id]["players"] as $player => $point){
                $players[] = $player;
            }
            return "§7" . implode("§f, §7", $players) . "§f";
        }
        return "";
    }

    public static function sendStuff(Player $player){
        /* Armure */
        $helmet = Item::get(Item::LEATHER_HELMET);
        $chestplate = Item::get(Item::LEATHER_CHESTPLATE);
        $leggings = Item::get(Item::LEATHER_LEGGINGS);
        $boots = Item::get(Item::LEATHER_BOOTS);
        $helmet->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 1));
        $chestplate->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 1));
        $leggings->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 1));
        $boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 1));
        $player->getArmorInventory()->setHelmet($helmet);
        $player->getArmorInventory()->setChestplate($chestplate);
        $player->getArmorInventory()->setLeggings($leggings);
        $player->getArmorInventory()->setBoots($boots);
        /* Inventaire */
        for ($i = 0;$i < 36;$i++){
            $player->getInventory()->setItem($i, Item::get(Item::SANDSTONE, 0, 64));
        }
        $sword = Item::get(Item::IRON_SWORD);
        $sword->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::SHARPNESS), 3));
        $sword->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::KNOCKBACK), 1));
        $player->getInventory()->setItem(0, $sword);
        $pickaxe = Item::get(Item::IRON_PICKAXE);
        $pickaxe->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::EFFICIENCY), 3));
        $player->getInventory()->setItem(1, $pickaxe);
        $gapp = Item::get(Item::GOLDEN_APPLE, 0, 64);
        $player->getInventory()->setItem(2, $gapp);
    }

    public static function stopHikabrain(string $id, string $reason){
        foreach (Server::getInstance()->getLevelByName(self::$hikabrain[$id]["waitingRoom"])->getPlayers() as $player){
            $player->sendMessage(Manager::ANONCEUR . "Téléportation au spawn.");
            Functions::goToSpawn($player);
        }
        Server::getInstance()->getLevelByName(self::$hikabrain[$id]["waitingRoom"])->unload(true);
        Functions::removeDir(Server::getInstance()->getDataPath() . "/worlds/" . self::$hikabrain[$id]["waitingRoom"]);
        foreach (Server::getInstance()->getLevelByName(self::$hikabrain[$id]["name"])->getPlayers() as $player){
            $player->sendMessage(Manager::ANONCEUR . "Téléportation au spawn.");
            Functions::goToSpawn($player);
        }
        Server::getInstance()->getLevelByName(self::$hikabrain[$id]["name"])->unload(true);
        Functions::removeDir(Server::getInstance()->getDataPath() . "/worlds/" . self::$hikabrain[$id]["name"]);
        foreach (self::$hikabrain[$id]["players"] as $player => $points){
            $p = Server::getInstance()->getPlayerExact($player);
            if ($p instanceof Player){
                $p->sendMessage(Manager::ANONCEUR . "La partie est terminer. Raison: §7{$reason}§f.");
            }
        }
        foreach (self::$scoreboard as $player => $value){
            unset(self::$scoreboard[$player]);
        }
        foreach (self::$players as $player => $hikabrain){
            if ($hikabrain === $id){
                unset(self::$players[$player]);
            }
        }
        foreach (PrivateGameManager::$private as $player => $value){
            if (PrivateGameManager::$private[$player]["game"] === $id){
                unset(PrivateGameManager::$private[$player]);
            }
        }
        Webhook::sendGameLogs("> L'hikabrain ayant pour id **" . $id . "** vient d'être stopper pour la raison **$reason**.");
        unset(self::$hikabrain[$id]);
    }

    public static function sendMessageForAll(string $id, Player $player, string $message){
        if (isset(self::$hikabrain[$id])) {
            foreach (self::$hikabrain[$id]["players"] as $pla => $pla) {
                $p = Server::getInstance()->getPlayerExact($pla);
                if ($p instanceof Player) {
                    $p->sendMessage("§7[" . TeamManager::getTeamOfPlayer($id, $player, true) . "§7] " . $player->getName() . " §f- §7" . $message);
                }
            }
        }
    }

    public static function isInGame(Player $player): bool{
        if (isset(self::$players[$player->getName()])){
            return true;
        }
        return false;
    }

    public static function getStatus(string $id): string{
        return self::$hikabrain[$id]["status"];
    }

    public static function getId(Player $player): string{
        if (self::isInGame($player)){
            return self::$players[$player->getName()];
        }
    }

}